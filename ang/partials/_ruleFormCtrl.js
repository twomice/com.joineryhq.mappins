(function(angular, $, _) {

  // Controller for the "Extension Overlay" dialog content.
  angular.module('mappins').controller('ruleFormCtrl', function ruleFormCtrl($scope, crmApi, crmStatus, crmUiHelp, dialogService, $q, $timeout) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mappins');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/mappins/rule'}); // See: templates/CRM/mappins/rules.hlp

    /**
     * Add action-link methods to the given rule object. These methods
     * will be called by click events in the UI, for example, "Enable" or "Delete".
     *
     * @param obj An object defining a rule, in the same format as one
     *   provided by, for example, crmApi('MappinsRule', 'getsingle').
     */
    var addActionMethods = function addActionMethods(obj) {
      obj.edit = function edit() {
        $scope.openRuleForm(this);    
      };
      
      obj.disable = function disable() {
        rule = this;
        return crmStatus(
          // Status messages. For defaults, just use "{}"
          {start: ts('Disabling...'), success: ts('Disabled')},
          crmApi('MappinsRule', 'create', {
            id: rule.rule_id,
            is_active: 0
          })
        )
        .then(function(result){
          rule.is_active = 0;            
        });
      };
      
      obj.enable = function enable() {
        rule = this;
        return crmStatus(
          // Status messages. For defaults, just use "{}"
          {start: ts('Enabling...'), success: ts('Enabled')},
          crmApi('MappinsRule', 'create', {
            id: rule.rule_id,
            is_active: 1
          })
        )
        .then(function(result){
          rule.is_active = 1;            
        });
      };
      
      obj.del = function del(index, viewName) {
        rule = this;
        console.log('index', index)
        console.log(', viewName', viewName)
//        $scope.rules[viewName].splice(index, 1);
        
        return crmStatus(
          // Status messages. For defaults, just use "{}"
          {start: ts('Deleting...'), success: ts('Deleted')},
          crmApi('MappinsRule', 'delete', {
            id: rule.rule_id,
          })
        )
        .then(function(result) {
          $scope.loadSelectedProfileRules();
        });
      };
    };

    
    $scope.setSelectedProfile = function setSelectedProfile(profile) {
      $scope.selectedProfile = profile;
    }
    
    $scope.loadSelectedProfileRules = function loadSelectedProfileRules() {
      var baseParams = {
        "options": {
          "limit": 0,
          "sort": "weight"
        }
      }
      if ($scope.selectedProfile.id > 0) {
        selectedProfileParams = baseParams;
        selectedProfileParams.uf_group_id = $scope.selectedProfile.id;
        var apiProfiles = crmApi('MappinsRuleProfile', 'get', selectedProfileParams);
        $q.all([apiProfiles])
        .then(function(values){
          var rules = $.map(values[0].values, function(value, index) {
            return [value];
          });
          rules.sort(function(a, b){
            return a.weight > b.weight;
          })
          rules = _.each(rules, function(obj){
            addActionMethods(obj)
          })
          
          $scope.rules.selectedProfile = rules;
        });      
      }
      
      unassignedParams = baseParams;
      unassignedParams.uf_group_id = {"IS NULL":1};
      var apiUnassigned = crmApi('MappinsRuleProfile', 'get', unassignedParams);
      $q.all([apiUnassigned])
      .then(function(values){
        var rules = $.map(values[0].values, function(value, index) {
          return [value];
        });
        rules.sort(function(a, b){
          return a.weight > b.weight;
        })        
        rules = _.each(rules, function(obj){
          addActionMethods(obj)
        })
        
        $scope.rules.unassigned = rules;
      });      


      
    }
    
    $scope.openRuleForm = function openRuleForm(rule) {
      if (typeof rule === 'undefined') {
        var title = ts('Create new rule');
        var rule = {}
      }
      else {
        var title = ts('Edit rule');
      }
      var options = CRM.utils.adjustDialogDefaults({
        autoOpen: false,
        title: title
      });
      
      var model = {
        rule: rule,
        profiles: $scope.profiles
      };
      dialogService.open('mappins-rule-form', '~/mappins/ruleFormCtrl.html', model, options)
      
      var setOverlayButtons = function setOverlayButtons() {
        var buttons = [
          {
            text: ts('Save'),
            click: function() {
              alert('fixme: save button');
              dialogService.close('mappins-rule-form');
            }
          },
          {
            text: ts('Cancel'),
            icons: {primary: 'fa-times'},
            click: function() {
              dialogService.cancel('mappins-rule-form');
            }
          }
        ]
        dialogService.setButtons('mappins-rule-form', buttons);
      }
      $timeout(setOverlayButtons)
    }
    
    $scope.saveWeights = function saveWeights(e, ui) {
      if (e.target.id == 'selectedProfileRules') {
        model = $scope.rules.selectedProfile;
      }
      else if (e.target.id == 'unassignedRules') {
        model = $scope.rules.unassigned;
      }
      for (var i in model) {
        var id = model[i].id;
        var params = {
          'id': id,
          'weight': i
        };
        CRM.api3('MappinsRuleProfile', 'create', params, ts('Order saved'));
      }
    }
    
    $scope.openKCFinder = function openKCFinder(e, index, viewName) {
      e.preventDefault();
      window.KCFinder = {
        callBack: function (url) {
          window.KCFinder = null;
          console.log('rule', $scope.rules[viewName][index]);
          $scope.rules[viewName][index].image_url = url;
          $scope.$apply();
          
          params = {
            'id': $scope.rules[viewName][index].rule_id,
            'image_url': url
          };
          CRM.api3('MappinsRule', 'create', params, ts('Image saved'));
        }
      };

      window.open(
        '/sites/all/modules/civicrm/packages/kcfinder/browse.php?cms=civicrm&type=images',
        'kcfinder_textbox_image_url',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, resizable=1, scrollbars=0, width=800, height=600'
      );
    }
    
    
    var apiProfiles = crmApi('uf_group', 'get', {
      return: ['id', 'title'],
      sequential: 1
    });
    $q.all([apiProfiles])
    .then(function(values){
      profiles = values[0].values;
      profiles.push({
        'id': 0,
        'title': '(All profiles / fallback)'
      });
      $scope.profiles = profiles;
      // Set the page to start with "All profiles / fallback":
      $scope.selectedProfile = _.findWhere($scope.profiles, {'id': '16'});
      $scope.loadSelectedProfileRules();
    });
    
    $scope.rules = {};
    $scope._ = _;
    window.scope = $scope;
  
    
  });
    

})(angular, CRM.$, CRM._);

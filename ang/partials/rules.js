(function(angular, $, _) {

  angular.module('mappins').config(function($routeProvider) {
      $routeProvider.when('/mappins/rules', {
        controller: 'Mappinsrules',
        templateUrl: '~/mappins/rules.html',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
        }
      });
    }
  );

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   dialogService -- provided by CiviCRM
  //   $q, $timeout -- provided by angular.
  //   myContact -- The current contact, defined above in config().
  angular.module('mappins').controller('Mappinsrules', function($scope, crmApi, crmStatus, crmUiHelp, dialogService, $q, $timeout) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mappins');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/mappins/rules'}); // See: templates/CRM/mappins/rules.hlp
    
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
          $scope.rules.selectedProfile = $.map(values[0].values, function(value, index) {
            return [value];
          });
          $scope.rules.selectedProfile.sort(function(a, b){
            return a.weight > b.weight;
          })
        });      
      }
      
      unassignedParams = baseParams;
      unassignedParams.uf_group_id = {"IS NULL":1};
      var apiUnassigned = crmApi('MappinsRuleProfile', 'get', unassignedParams);
      $q.all([apiUnassigned])
      .then(function(values){
        $scope.rules.unassigned = $.map(values[0].values, function(value, index) {
          return [value];
        });
        $scope.rules.unassigned.sort(function(a, b){
          return a.weight > b.weight;
        })
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
      dialogService.open('mappins-rule-create', '~/mappins/ruleFormCtrl.html', rule, options)
      
      var setOverlayButtons = function setOverlayButtons() {
        var buttons = [
          {
            text: ts('Save'),
            click: function() {
              alert('fixme: save button');
              dialogService.close('mappins-rule-create');
            }
          },
          {
            text: ts('Cancel'),
            icons: {primary: 'fa-times'},
            click: function() {
              dialogService.cancel('mappins-rule-create');
            }
          }
        ]
        dialogService.setButtons('mappins-rule-create', buttons);
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
        var a = CRM.api3('MappinsRuleProfile', 'create', params, ts('Order saved'));
      }
    }
    
    $scope.openKCFinder = function openKCFinder() {
      var field = this;
      window.KCFinder = {
        callBack: function (url) {
          console.log('url', url);
          return;
          
          var ruleId = CRM.$(field).attr('data-rule-id');
          CRM.$('img#crm-mappinsrule-image-'+ ruleId).attr('src', url);
          window.KCFinder = null;

          saveRuleImageUrl(ruleId, url);
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



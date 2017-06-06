(function(angular, $, _) {

  angular.module('mappins').config(function($routeProvider) {
      $routeProvider.when('/mappins/rules/:profileId?', {
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
  angular.module('mappins').controller('Mappinsrules', function($scope, crmApi, crmStatus, crmUiHelp, dialogService, $q, $timeout, $location, $routeParams, $rootScope) {

    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mappins');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/mappins/rules'}); // See: templates/CRM/mappins/rules.hlp

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

    $scope.criteriaOptions = {
      'contact_sub_type': ts('Contact Sub Type'),
      'group': ts('Group ID'),
      'tag': ts('Tag ID'),
    };    
    
    $scope.setSelectedProfile = function setSelectedProfile(profile) {
      $scope.selectedProfile = profile;
    }
    
    $scope.loadSelectedProfileRules = function loadSelectedProfileRules() {
      if (typeof $scope.selectedProfile == 'undefined') {
        return;
      }
      
      if ($scope.selectedProfile.id > 0) {
        console.log('===============', $scope.allRules);
        $scope.rules.selectedProfile = _.filter($scope.allRules, function(rule){
          console.log('profileid:' + $scope.selectedProfile.id, rule.uf_group_id, rule.uf_group_id.indexOf($scope.selectedProfile.id));
          return (rule.uf_group_id.indexOf($scope.selectedProfile.id) > -1);
        });
      }
      console.log('===============', $scope.allRules);
      $scope.rules.unassigned = _.filter($scope.allRules, function(rule){
        console.log('profileid:' + 0 +'; matches: '+ rule.uf_group_id.indexOf(0), rule);
        return (rule.uf_group_id.indexOf(0) > -1);
      });
      return;
      
      
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
    
    $scope.loadAll = function loadAll() {
      var apiRules = crmApi('MappinsRule', 'get', {
        "sequential": 1,
        "options": {"sort":"id"}
      });
      $q.all([apiRules])
      .then(function(values){
        $scope.allRules = values[0].values;
        for (i in $scope.allRules) {
          $scope.allRules[i].rule_id = $scope.allRules[i].id;
          for (u in $scope.allRules[i].uf_group_id) {
            if ($scope.allRules[i].uf_group_id[u] == 'NULL') {
              $scope.allRules[i].uf_group_id[u] = 0;
            }
          }
        }
      });      
    }
    
    $scope.openKCFinder = function openKCFinder(index, viewName) {
      window.KCFinder = {
        callBack: function (url) {
          window.KCFinder = null;
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
    
    $scope.getProfileTitle = function getProfileTitle(profileId) {
      if (typeof profileId == 'undefined') {
        profileId = 0;
      }
      var profile = _.findWhere($scope.profiles, {'id': profileId});
      if (_.isObject(profile)) {
        return profile.title;
      }
      else {
        return '';
      }
    }
    
    $scope.$watch('selectedProfile', function() {
      $scope.loadSelectedProfileRules();      
    }, true);

    // FIXME: probably should do this in resolve:{}.
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
      $scope.selectedProfile = _.findWhere($scope.profiles, {'id': $routeParams.profileId});
    });
    
    $scope.rules = {};
    $scope._ = _;
    window.scope = $scope;
    
    // Pass $location service so we can use it in Add button ng-click.
    $scope.$location = $location;
  
    // Load "loadAll" for "All" tab.
    $scope.loadAll();
    
    // CiviCRM Core doesn't yet support active tab in crm-ui-tabs, but I'm working on that.
    // Pass the active tab index to $scope so crm-ui-tabs can use it.
    $scope.activeTabIndex = $routeParams.tid;
    
    // Sloppy history tracking. Useful for 
    $scope.$on('$locationChangeStart', function(evt, newUrl, oldUrl) {
      if (typeof $rootScope.mappins.history == 'undefined' ) {
        $rootScope.mappins.history = [];
      }      
      $rootScope.mappins.history.push(oldUrl);
    });
    
  });
  
  
})(angular, CRM.$, CRM._);



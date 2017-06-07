(function(angular, $, _) {

  angular.module('mappins').config(function($routeProvider) {
      $routeProvider.when('/mappins/rule/:id?', {
        controller: 'MappinsruleEditCtrl',
        templateUrl: '~/mappins/ruleEditCtrl.html',

        // If you need to look up data when opening the page, list it out
        // under "resolve".
        resolve: {
          rule: function(crmApi, $route) {
            var rule_id = $route.current.params.id;
            if (
              !isNaN(rule_id) && 
              parseInt(Number(rule_id)) == rule_id && 
              !isNaN(parseInt(rule_id, 10))
            ){
              return crmApi('mappinsRule', 'get', {
                id: rule_id,
                sequential: 1
              });              
            }
            else {
              return false;
            }
          },
          profiles: function(crmApi) {
            return crmApi('uf_group', 'get', {
              return: ['id', 'title'],
              sequential: 1
            });
          }
        }
      });
    }
  );

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   myContact -- The current contact, defined above in config().
  angular.module('mappins').controller('MappinsruleEditCtrl', function($scope, crmApi, crmStatus, crmUiHelp, rule, profiles, $routeParams, $location, $window) {

    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mappins');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/mappins/ruleEditCtrl'}); // See: templates/CRM/mappins/ruleEditCtrl.hlp

    profiles.values.push({
      'id': '0',
      'title': '(All profiles / fallback)'
    });    
    $scope.profiles = profiles.values;    
    
    if (_.isObject(rule)) {
      $scope.rule = rule.values[0];
      for (i in $scope.rule.uf_group_id) {
        if ($scope.rule.uf_group_id[i] == 'NULL') {
          $scope.rule.uf_group_id[i] = '0';
        }
      }    
    }
    else {
      $scope.rule = {};
    }
    
    $scope.criteriaOptions = {
      'contact_sub_type': ts('Contact Sub Type'),
      'group': ts('Group ID'),
      'tag': ts('Tag ID'),
    };    
    
    // Pass $window service so we can use it in Cancel button ng-click.
    $scope.cancel = function cancel() {
      goToDestination();
    }
    
    var goToDestination = function goToDestination() {
      var destination;
      if (typeof $routeParams.destination != 'undefined' && $routeParams.destination.length) {
        var query = {}
        query.tid = $routeParams.destinationTab;
        destination = CRM.url('civicrm/a/#' + $routeParams.destination, query);
      }
      else {
        destination = CRM.url('civicrm/a/#/mappins/rules');        
      }
      $window.location.href = destination;

    };

    $scope.save = function save() {
      return crmStatus(
        // Status messages. For defaults, just use "{}"
        {start: ts('Saving...'), success: ts('Saved')},
        // The save action. Note that crmApi() returns a promise.        
        crmApi('MappinsRule', 'create', $scope.rule)
        .then(function(values) {
          goToDestination();
        })
      );
    };

    $scope.openKCFinder = function openKCFinder(index, viewName) {
      window.KCFinder = {
        callBack: function (url) {
          window.KCFinder = null;
          $scope.rule.image_url = url;
          $scope.$apply();
        }
      };

      window.open(
        '/sites/all/modules/civicrm/packages/kcfinder/browse.php?cms=civicrm&type=images',
        'kcfinder_textbox_image_url',
        'status=0, toolbar=0, location=0, menubar=0, directories=0, resizable=1, scrollbars=0, width=800, height=600'
      );
    }
    
  });

})(angular, CRM.$, CRM._);

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

    var apiProfiles = crmApi('uf_group', 'get', {
      return: ['id', 'title'],
      sequential: 1
    });
    $q.all([apiProfiles])
    .then(function(values){
      profiles = values[0].values;
      profiles.push({
        'id': 0,
        'title': '(All profiles)'
      });
      $scope.profiles = profiles;
      $scope.selectedProfile = {};
    });
    
    $scope.setSelectedProfile = function setSelectedProfile(profile) {
      $scope.selectedProfile = profile;
    }
    
    $scope.loadSelectedProfileRules = function() {
      alert('fixme: loadSelectedProfileRules()')
      console.log('$scope.selectedProfile', $scope.selectedProfile);
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
  });
})(angular, CRM.$, CRM._);

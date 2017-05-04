/**
 * Custom Javascript for "Map Pins: Rules" listing page.
 * @type type
 */
CRM.$(function(){
  CRM.$('table#options tbody').sortable(
    {
      axis: 'y',
      update: function (event, ui) {
        var orderedRowIds = CRM.$(this).sortable("toArray");
        for (i in orderedRowIds) {
          var id = orderedRowIds[i].split('-').pop();
          CRM.api3('MappinsRule', 'create', {
            'id': id,
            'weight': i
          },
          true);
        }
        
        // Alternativng stripe colors are probably wrong now. Re-stripe the rows.
        var stripingClasses = [
          'odd-row',
          'even-row'
        ]        
        CRM.$('table#options tbody tr').each(function(idx, el){
          var addClass = stripingClasses.shift();
          CRM.$(el).addClass(addClass);
          CRM.$(el).removeClass(stripingClasses[0]);
          stripingClasses.push(addClass);
        })
        
        return;
      }
    }          
  );
  
  function openKCFinder() {
    var field = this
    window.KCFinder = {
      callBack: function (url) {
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
  
  function saveRuleImageUrl(ruleId, url) {
    CRM.api3('MappinsRule', 'create', {
      'id': ruleId,
      'image_url': url
    },
    true);
  }

  function initializeImageKcfinder() {
    CRM.$('a.crm-mappins-rule-image-button').click(openKCFinder);
  }

  initializeImageKcfinder();
})
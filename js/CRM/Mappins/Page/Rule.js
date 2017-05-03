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
  
  // Update image.
  CRM.$( document ).ajaxComplete(function( event, xhr, settings ) {
    if (settings.url == "/civicrm/ajax/rest") {
       var queryParams = parseQuery(settings.data);
       if (queryParams.entity == 'mappins_rule') {
         var json = CRM.$.parseJSON(queryParams.json)
         if (json.image_url && json.id) {
           CRM.$('img#crm-mappinsrule-image-'+ json.id).attr('src', json.image_url);
         }
       }
    }
  });
  
  /**
   * Return a given query string as an object.
   * @param {type} qstr
   * @returns {unresolved}
   */ 
  function parseQuery(qstr) {
    var query = {};
    var a = (qstr[0] === '?' ? qstr.substr(1) : qstr).split('&');
    for (var i = 0; i < a.length; i++) {
        var b = a[i].split('=');
        query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
    }
    return query;
  }
})
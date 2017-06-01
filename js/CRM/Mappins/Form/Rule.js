/**
 * Custom Javascript for "Map Pins: Edit Rule" form.
 * @type type
 */
CRM.$(function () {
  function openKCFinder() {
    window.KCFinder = {
      callBack: function (url) {
        CRM.$('input[name="image_url"]').val(url);
        CRM.$('img#mappinsrule-image-preview').attr('src', url);
        window.KCFinder = null;
      }
    };

    window.open(
      '/sites/all/modules/civicrm/packages/kcfinder/browse.php?cms=civicrm&type=images', 
      'kcfinder_textbox_image_url',
      'status=0, toolbar=0, location=0, menubar=0, directories=0, resizable=1, scrollbars=0, width=800, height=600'
    );
  }


  function initializeImageKcfinder() {
    CRM.$('a#mappinsrule-image-button').click(openKCFinder);
  }

  initializeImageKcfinder();

  // In some cases (e.g., form validation failure) the image_url field may 
  // have a value, while the image button has no src. Fix that.
  if (!CRM.$('img#mappinsrule-image-preview').attr('src')) {          
    CRM.$('img#mappinsrule-image-preview').attr('src', CRM.$('input[name="image_url"]').val());
  }
});
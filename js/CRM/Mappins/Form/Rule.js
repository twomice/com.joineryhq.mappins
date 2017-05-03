/**
 * Custom Javascript for "Map Pins: Edit Rule" form.
 * @type type
 */
CRM.$(function () {
  function openKCFinder() {
    var field = this
    window.KCFinder = {
      callBack: function (url) {
        CRM.$(field).val(url);
        CRM.$(field).change();
        window.KCFinder = null;

      }
    };

    window.open(
      '/sites/all/modules/civicrm/packages/kcfinder/browse.php?cms=civicrm&type=images', 
      'kcfinder_textbox_image_url',
      'status=0, toolbar=0, location=0, menubar=0, directories=0, resizable=1, scrollbars=0, width=800, height=600'
    );
  }

  function imageUrlChange() {
    CRM.$('img#mappinsrule-image-preview').attr('src', CRM.$('input#image_url').val());
  }

  function initializeImageKcfinder() {
    CRM.$('input#image_url').click(openKCFinder);
    CRM.$('input#image_url').attr('readonly', 'readonly');
    CRM.$('tr#image_url_tr').addClass('has-kcfinder');
  }

  initializeImageKcfinder();
  CRM.$('input#image_url').change(imageUrlChange);
})
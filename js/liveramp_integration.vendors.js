(function (Drupal, $, drupalSettings) {
  $(window).ready(function () {

    let handle = setInterval(function () {
      if (undefined !== this.gdprConfiguration) {
        this.gdprConfiguration.noticeConfig.display = false;
        setVendorList(this);
        clearInterval(handle);
      }
    }, 50);

    let killId = setTimeout(function () {
      for (let i = killId; i > 0; i--) {
        clearInterval(i);
      }
    }, 2000);
  });

  function setVendorList(w) {
    w.__tcfapi('getVendorList', null, (tcData, success) => {
      let data = '<p>';
      jQuery.each(tcData.vendors, function (index, value) {
        data = data + '<span> ID: ' + value.id + ' - NAME: ' + value.name + '</span><br />';
      });
      data += '</p>';
      jQuery('#liveramp_integration-vendors').find('.details-wrapper').append(data);
    });
  }

})(Drupal, jQuery, drupalSettings);

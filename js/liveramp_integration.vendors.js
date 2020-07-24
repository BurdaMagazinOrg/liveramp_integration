(function ($) {
  let handle = setInterval(function () {
    if (undefined !== window.__tcfapi) {
      setVendorList($);
      clearInterval(handle);
    }
  }, 50);

  let killId = setTimeout(function () {
    for (let i = killId; i > 0; i--) {
      clearInterval(i);
    }
  }, 2000);
})(jQuery);

function setVendorList($) {
  window.__tcfapi('getVendorList', null, (tcData, success) => {
    let data = '<p>';
    $.each(tcData.vendors, function (index, value) {
      data = data + '<span> ID: ' + value.id + ' - NAME: ' + value.name + '</span><br />';
    });
    data += '</p>';
    $('#liveramp_integration-vendors').find('.details-wrapper').append(data);
  });
}

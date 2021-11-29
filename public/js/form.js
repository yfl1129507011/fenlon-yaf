$(function () {
    $('#get-captcha').on('click', function () {
        var img = $('#captcha');
        var imgSrc = img.attr('src');
        var index = imgSrc.indexOf('?');
        if (index > 0) {
            imgSrc = imgSrc.substring(0, index);
        }
        var newSrc = imgSrc + '?' + Math.random();
        img.attr('src', newSrc);
    })
})
function networkUtils(options) {
    return new Promise(function (resolve, reject) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax(options).done(resolve).fail(reject);
    });
}

function openModal(NodeNameContent) {
    $('#interestModal').removeClass('invisible hidden');
    $(window).on('click', function (e) {
        if ($('.inner-modal').is($(e.target)))
            $('#interestModal').addClass('invisible hidden');
    });
    $('.modal-close').on('click', function (e) {
        $('#interestModal').addClass('invisible hidden');
        $(NodeNameContent).html('')
    });
}

function onChangeBtnSubMenu (localStorageName, callback) {
    var x = localStorage.getItem(localStorageName);
    if(x) {
        $('.btn-sub-menu').each(function(e) {
            var sameValue = $(this).val() == x;
            if(sameValue)
                $(this).addClass('text-green-700 border-b-2 border-green-700 active')
                
        });
    }

    $('.btn-sub-menu').on('click', function(e) {
        $('.btn-sub-menu').each(function(e) {
            var hasClass = $(this).hasClass( "active" );
            if(hasClass)
                $(this).removeClass('text-green-700 border-b-2 border-green-700 active')
                
        });
        $(this).toggleClass("text-green-700 border-b-2 border-green-700 active");
        
        $('#status-user').val($(this).val());
        var type = $(this).val();
        var x = localStorage.getItem(localStorageName);
    console.log("localStorageName",x == type)
        if(x != type) {
            localStorage.setItem(localStorageName, type);
            callback(type)
        }
    });
}

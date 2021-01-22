document.addEventListener('DOMContentLoaded', function() {
    $('#modal-save').on('click', function() {
        $.ajax({
                method: 'GET',
                url: url_deposite,
                beforeSend: function(xhr) {
                    alert(token);
                    axios.defaults.headers.common['Authorization'] = token;
                    xhr.setRequestHeader('Authorization', 'Bearer ' + token);
                },
                headers: {
                    'Authorization': token,
                },
                data: {}
            })
            .done(function(data) { //when ajax request done successfuly
                alert(data.deposites);
            });
    });
});
$(document).on('change', '#lieu_lieu', function (){
    let $field = $(this)
    let $form = $field.closest('form')
    let data = {}
    data[$field.attr('name')] = $field.val()
    $.post($form.attr('action'), data).then(function (data){
        let $input_rue = $(data).find('#lieu_rue')
        $('#lieu_rue').replaceWith($input_rue)

        let $input_codepostal = $(data).find('#lieu_codepostal')
        $('#lieu_codepostal').replaceWith($input_codepostal)

        let $input_longitude = $(data).find('#lieu_longitude')
        $('#lieu_longitude').replaceWith($input_longitude)

        let $input_latitude = $(data).find('#lieu_latitude')
        $('#lieu_latitude').replaceWith($input_latitude)

    })


})
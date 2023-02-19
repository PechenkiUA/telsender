tseventUrmHandller = (event) => {
    const elem = event.target

    if (elem.classList.contains('add')) {
        const copy = elem.closest('.utm_list').cloneNode(true)
        copy.querySelector('.add').remove()
        copy.querySelector('.remove').disabled = false

        document.querySelector('.utm_list').insertAdjacentHTML('afterend',copy.outerHTML)
    }

    if (elem.classList.contains('remove')) {
        const copy = elem.closest('.utm_list').remove()
    }
}
document.querySelector('.utm_list_wrap').addEventListener('click', tseventUrmHandller)





jQuery('.bot_list_wrap select').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    create: function (input) {
        return {
            value: input,
            value: input,
            text: input
        }
    }
});


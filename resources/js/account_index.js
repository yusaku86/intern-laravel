window.addEventListener('DOMContentLoaded', function () {
    const deleteBtns = document.querySelectorAll('.btn-delete');

    deleteBtns.forEach(deleteBtn => {
        deleteBtn.addEventListener('click', event => confirmBeforeDelete(deleteBtn.id.split('-').pop(), event));
    });
})


function confirmBeforeDelete(accountId, event) {
    const email = document.querySelector(`#email-${accountId}`);

    if (!window.confirm(`メールアドレス「${email.innerHTML}」のアカウントを削除します。よろしいですか?`)) {
        event.preventDefault();
    }
}

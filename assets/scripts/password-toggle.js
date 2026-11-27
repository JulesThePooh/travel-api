// Bouton "afficher / masquer" sur les champs mot de passe : <button data-password-toggle="#id">
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-password-toggle]')
    if (!button) return

    const input = document.querySelector(button.dataset.passwordToggle)
    if (!input) return

    const visible = input.type === 'text'
    input.type = visible ? 'password' : 'text'
    button.textContent = visible ? 'Afficher' : 'Masquer'
    button.setAttribute('aria-pressed', String(!visible))
})

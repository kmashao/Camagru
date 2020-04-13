//just a fancy screen for messages
document.addEventListener('DOMContentLoaded', () => {

	var modal = document.querySelector('.modal');
	var html = document.querySelector('html');
	modal.classList.add('is-active');
	html.classList.add('is-clipped');

	modal.querySelector('.modal-close').addEventListener('click', function (e) {
		e.preventDefault();
		modal.classList.remove('is-active');
		html.classList.remove('is-clipped');
		location.reload();
	})

	modal.querySelector('.modal-background').addEventListener('click', function (e) {
		e.preventDefault();
		modal.classList.remove('is-active');
		html.classList.remove('is-clipped');
		location.reload();
	});
});
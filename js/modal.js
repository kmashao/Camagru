//just a fancy screen for messages
document.addEventListener('DOMContentLoaded', () => {
	(document.querySelectorAll('.modal .modal-close') || []).forEach(($delete) => {
	  $modal = $delete.parentNode;
  
	  $delete.addEventListener('click', () => {
		$modal.parentNode.removeChild($modal);
	  });
	});

	const $modalClass = document.querySelectorAll('.modal');
	$modalClass.classList.toggle('is-active');
  });
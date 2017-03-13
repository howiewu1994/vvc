// $(document).ready(function(){
//
// });

$('button[name="delete_user"]').on('click', function(e) {
	var $form = $(this).closest('form');
	e.preventDefault();
	$('#confirm_delete').modal()
	.one('click', '#confirm_button', function(e) {
		$form.trigger('submit');
	});
});

// Autofocus confirm button
// $('#confirm_delete').on('shown.bs.modal', function () {
//   $('#confirm_button').focus()
// })

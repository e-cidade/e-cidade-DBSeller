<!DOCTYPE html>
<html>
<head>
  <title>Sessão bloqueada</title>
  <base href="<?php echo ECIDADE_CURRENT_EXTENSION_REQUEST_PATH; ?>"></base>
  <link rel="stylesheet" type="text/css" href="assets/css/Window/form.css">
  <link rel="stylesheet" type="text/css" href="assets/css/Window/block.css">
</head>
<body>

<div class="container">
  <form method="POST" action="v4/unblock">

    <div class="header">
      <div class="lock"></div>
      <div class="content">Sessão bloqueada</div>
    </div>

    <div class="input">
      <input type="password" id="senha" name="senha" placeholder="Senha" autofocus/>
    </div>

    <div class="input buttons">
      <button name="enviar" id="enviar" class="btn btn-primary" type="submit" >Desbloquear</button>
    </div>

  </form>

</div>

<script src="<?php echo ECIDADE_REQUEST_PATH; ?>/scripts/jquery-2.1.1.min.js"></script>
<script src="<?php echo ECIDADE_REQUEST_PATH; ?>/scripts/md5.js"></script>

<script type="text/javascript">

  (function($) {

    var CurrentWindow = window.frameElement.CurrentWindow;

    function close() {
      parent.alertify.alert("Sua sessão foi interrompida por inatividade.\nFavor fazer login novamente.", function() {
        // this é uma referência à window principal. Assim conseguindo acesso ao localStorage!
        // remove o token e manda recarregar, para que o usuário seja redirecionado a tela de login.
        this.localStorage.removeItem('ecidade@user_token')
        this.location.reload()
      }.bind(parent));
    }

    $(function($) {

      $('#senha').focus();

      $('form').on('submit', function(e) {

        e.preventDefault();
        $('#enviar').attr('disabled', true);

        CurrentWindow.loader.show();

        const token = JSON.parse(localStorage.getItem('ecidade@user_token'));
        $.ajax({
          url: '<?= ECIDADE_REQUEST_PATH ?>v4/unblock',
          data: {
              refresh_token: token.refresh_token,
              password: $('#senha').val()
          },
          type: 'POST',
          dataType: 'json'
        }).done(function(response) {
          localStorage.setItem('ecidade@user_token', JSON.stringify({
              access_token: response.data.access_token,
              expires_at: Date.now() + response.data.expires_in * 1000,
              refresh_token: response.data.refresh_token,
          }));

          CurrentWindow.close();

        }).fail(function(xhr) {

          CurrentWindow.loader.hide();

          $('#enviar').attr('disabled', false);
          var data = JSON.parse(xhr.responseText);

          if (data.message == 'Extensão desativada: Desktop') {
            return close();
          }
          if (data.message == 'The refresh token is invalid.') {
            return close();
          }

          $('form .error').remove();
          $('form input:first').after('<p class="error">' + data.message + '</p>').focus()
        }).always(function() {
          CurrentWindow.loader.hide();
        });

      });

    });

  })(jQuery);

</script>

</body>
</html>

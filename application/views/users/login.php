<!DOCTYPE html>
<html lang="es">
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    </head>
    <body class="teal lighten-2">    
        <div class="container">
            <div class="row">
                <div class="col offset-s0 s12 offset-m2 m8" id="maincontainer">
                    <div class="card">
                        <?php if(validation_errors()): ?>
                        <div class="card-panel red white-text">
                            <?php echo validation_errors(); ?>
                        </div>
                        <?php endif; ?>
                        <?php if(!empty($this->input->get('msg')) && $this->input->get('msg') == 1): ?>
                        <div class="card-panel red white-text">
                            Nombre de usuario o contraseña incorrecta
                        </div>
                        <?php endif; ?>
                        <?php echo form_open('users/dologin'); ?>
                            <div class="card-content">
                                <span class="card-title">Inicio de Sesión</span>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="username" type="text" name="username" class="validate" required>
                                        <label for="username" >Nombre de Usuario</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="password" type="password" name="password" class="validate" required>
                                        <label for="password">Contraseña</label>
                                    </div>
                                </div>
                                <div class="card-actions">
                                    <button id="submitButton" class="btn waves-effect waves-light right" value="submit" type="submit" name="action">Entrar
                                        <i class="material-icons right">send</i>
                                    </button>        
                                </div>
                            </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
        <style>
            #maincontainer {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .card-actions {
                padding-bottom: 2em;
            }

        </style>
    </body>
</html>
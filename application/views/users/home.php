<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <nav class="grey darken-4 white-text">
        <div class="nav-wrapper">
            <a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a>
            <a href="<?= site_url('/') ?>" class="brand-logo">Esteno</a>
            <ul id="nav-mobile" class="right hide-on-med-and-down" style="padding-right: 1em;">
                <li><a href="collapsible.html">Digitadores</a></li>
                <li><a href="sass.html">Errores</a></li>
                <li><a href="badges.html">Usuarios</a></li>
                <li><button class="btn waves-effect waves-light red darken-1" onclick=" document.location.href = '<?=site_url('users/logout')?>'">Cerrar Sesión</button></li>
            </ul>
        </div>
    </nav>
    <ul id="slide-out" class="sidenav grey darken-4 white-text">
        <li><a class="white-text" href="<?= site_url('/') ?>">Esteno</a></li>
        <li><a class="white-text" href="collapsible.html">Digitadores</a></li>
        <li><a class="white-text" href="sass.html">Errores</a></li>
        <li><a class="white-text" href="badges.html">Usuarios</a></li>
        <li class="red darken-1"><a class="white-text" href="'<?=site_url('users/logout')?>'">Cerrar sesión</li>
    </ul>

</body>

<style>
    .brand-logo {
        margin-left: 1em;
    }

    @media (max-width: 600px) {
        .brand-logo {
            margin: 0;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var elems = document.querySelectorAll('.sidenav');
        var instances = M.Sidenav.init(elems, {
            menuWidth: 250,
            edge: 'left',
            closeOnClick: false,
            draggable: true
        });
    });
</script>
</html>
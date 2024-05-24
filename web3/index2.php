<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5 col-6">
        <form id="formLogin">
            <div class="card">
                <div class="card-header">
                    <center>
                        <h2>Inicio de sesion</h2>
                    </center>
                </div>
                <div class="card-body">

                    <div class="form-floating mb-3">
                        <input type="usuario" class="form-control" name="usuario" id="usuario" placeholder="usuario">
                        <label for="floatingInput">Usuario</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" id="pass" name="pass" placeholder="Password">
                        <label for="pass">Password</label>
                    </div>

                </div>
                <div class="card-footer">
                    <button class="btn btn-primary" type="submit">Iniciar Sesion</button>
                    <a class="btn btn-secondary" href="/registro.php">Registrarse</a>
                </div>
            </div>
        </form>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $('#formLogin').on('submit', function(e) {
            e.preventDefault();
            // validation code here
            var usuario = $('#usuario').val();
            var pass = $('#pass').val();
            var datos = {
                'nick': usuario,
                'pass': pass
            }
            $.ajax({
                url: "controlador/controladorLogin.php",
                type: 'POST',
                data: JSON.stringify(datos),
                success: function(result) {
                    var resp = JSON.parse(result);
                    alert(resp.msg);
                }
            });
        });
    });
</script>

</html>
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
        <form action="/controlador/controladorRegistro.php" method="post">
        <div class="card">
            <div class="card-header">
                <h2>Registro</h2>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input required="true" type="text" name="nombre" class="form-control" id="nombre" >
                </div>
                <div class="mb-3">
                    <label for="papellido" class="form-label">primer apellido</label>
                    <input type="text" name="papellido" class="form-control" id="papellido">
                </div>
                <div class="mb-3">
                    <label for="sapellido" class="form-label">Segundo Apellido</label>
                    <input type="text" name="sapellido" class="form-control" id="sapellido" >
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label">nombre de usuario</label>
                    <input type="text" name="usuario" class="form-control" id="usuario" >
                </div>
                <div class="mb-3">
                    <label for="pass1" class="form-label">Contraseña</label>
                    <input type="text" name="pass1" class="form-control" id="pass1">
                </div>
                <div class="mb-3">
                    <label for="pass2" class="form-label">Repita la contraseña</label>
                    <input type="text" onblur="validar();" name="pass2" class="form-control" id="pass2">
                </div>

            </div>
            <div class="card-footer">
                <button class="btn btn-primary" type="submit">Registrarse</button>
                <a class="btn btn-secondary" href="/index.php">volver</a>
                
            </div>
        </div>
        </form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
<script>
    function validar(){
        var pass1=document.getElementById("pass1").value;
        var pass2=document.getElementById("pass2").value;
        if(pass1!=pass2){
            alert("las claves no coinciden");
        }

    }
</script>
</html>
<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "tbl_product");

if (isset($_POST["add_to_cart"])) {
    if (isset($_SESSION["shopping_cart"])) {
        $item_array_id = array_column($_SESSION["shopping_cart"], "item_id");
        if (!in_array($_GET["id"], $item_array_id)) {
            $count = count($_SESSION["shopping_cart"]);
            $item_array = array(
                'item_id'           =>  $_GET["id"],
                'item_name'         =>  $_POST["hidden_name"],
                'item_price'        =>  $_POST["hidden_price"],
                'item_quantity'     =>  $_POST["quantity"]
            );
            $_SESSION["shopping_cart"][$count] = $item_array;
        } else {
            echo '<script>alert("Producto ya fue agregado")</script>';
        }
    } else {
        $item_array = array(
            'item_id'           =>  $_GET["id"],
            'item_name'         =>  $_POST["hidden_name"],
            'item_price'        =>  $_POST["hidden_price"],
            'item_quantity'     =>  $_POST["quantity"]
        );
        $_SESSION["shopping_cart"][0] = $item_array;
    }
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        foreach ($_SESSION["shopping_cart"] as $keys => $values) {
            if ($values["item_id"] == $_GET["id"]) {
                unset($_SESSION["shopping_cart"][$keys]);
                echo '<script>alert("Producto retirado")</script>';
                echo '<script>window.location="index.php"</script>';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>

<head>
    <title>ConfiguroWeb</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <style>
        body {
            background: #1c1c1c; /* Fondo gris oscuro */
            font-family: 'Times New Roman', Times, serif;
            color: #e0e0e0; /* Texto en gris claro */
        }

        .container {
            background: #333333; /* Fondo gris más claro para contenedores */
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
        }

        h3 {
            color: #ff5722; /* Títulos en naranja oscuro */
        }

        .table-responsive {
            background: #424242; /* Fondo gris oscuro para tablas */
            border-radius: 15px;
            padding: 20px;
        }

        .table th, .table td {
            color: #e0e0e0; /* Texto en gris claro */
        }

        .btn-success {
            background: #4caf50; /* Botón verde */
            border: none;
        }

        .btn-success:hover {
            background: #388e3c; /* Botón verde oscuro */
        }

        footer {
            background-color: #2e2e2e; /* Fondo gris muy oscuro para el footer */
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        .product-card {
            border: 2px solid #d4af37; /* Borde dorado */
            background-color: #424242; /* Fondo gris oscuro */
            border-radius: 15px;
            padding: 16px;
            margin-bottom: 20px;
        }

        .product-card img {
            border-radius: 15px;
        }

        .text-danger {
            color: #f44336; /* Rojo oscuro */
        }

        a {
            color: #64b5f6; /* Enlaces en azul claro */
        }

        a:hover {
            color: #1e88e5; /* Enlaces en azul oscuro */
        }
    </style>
</head>

<body>
    <br />
    <div class="container">
        <br />
        <br />
        <br />
        <h3 align="center"><a href="https://www.configuroweb.com/" title="Para más desarrollos ConfiguroWeb">Bienvenido</a></h3><br />
        <br /><br />
        <div class="row">
            <?php
            $query = "SELECT * FROM tbl_product ORDER BY id ASC";
            $result = mysqli_query($connect, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_array($result)) {
            ?>
                    <div class="col-md-4">
                        <form method="post" action="index.php?action=add&id=<?php echo $row["id"]; ?>">
                            <div class="product-card" align="center">
                                <img src="images/<?php echo $row["image"]; ?>" class="img-responsive" /><br />

                                <h4><?php echo $row["name"]; ?></h4>

                                <h4 class="text-danger">$ <?php echo $row["price"]; ?></h4>

                                <input type="text" name="quantity" value="1" class="form-control" />

                                <input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />

                                <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />

                                <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-success" value="Agregar Producto" />

                            </div>
                        </form>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <div style="clear:both"></div>
        <br />
        <h3>Información de la Orden</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <tr>
                    <th width="40%">Nombre del Producto</th>
                    <th width="10%">Cantidad</th>
                    <th width="20%">Precio</th>
                    <th width="15%">Total</th>
                    <th width="5%">Acción</th>
                </tr>
                <?php
                if (!empty($_SESSION["shopping_cart"])) {
                    $total = 0;
                    foreach ($_SESSION["shopping_cart"] as $keys => $values) {
                ?>
                        <tr>
                            <td><?php echo $values["item_name"]; ?></td>
                            <td><?php echo $values["item_quantity"]; ?></td>
                            <td>$ <?php echo $values["item_price"]; ?></td>
                            <td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2); ?></td>
                            <td><a href="index.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Quitar Producto</span></a></td>
                        </tr>
                    <?php
                        $total = $total + ($values["item_quantity"] * $values["item_price"]);
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right">Total</td>
                        <td align="right">$ <?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                <?php
                }
                ?>

            </table>
        </div>
    </div>
    <br />
    <footer>
        <p>Todos los derechos reservados Andrey_R</p>
    </footer>
</body>

</html>

<?php
//Si ha utilizado una versión anterior de PHP, descomente esta función para eliminar el error.

/*function array_column($array, $column_name)
{
    $output = array();
    foreach($array as $keys => $values)
    {
        $output[] = $values[$column_name];
    }
    return $output;
}*/
?>

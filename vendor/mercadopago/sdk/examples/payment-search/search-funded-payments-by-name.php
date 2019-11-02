<!doctype html>
<html>
    <head>
        <title>Search funded payments with 12 installments and product name "product_name"</title>
    </head>
    <body>
        <?php
        /**
         * MercadoPago SDK
         * Search funded payments with 12 installments and product name "product_name"
         * @date 2012/03/29
         * @author hcasatti
         */
        // Include Mercadopago library
        require_once "../../lib/mercadopago.php";

        // Create an instance with your MercadoPago credentials (CLIENT_ID and CLIENT_SECRET): 
        // Argentina: https://www.mercadopago.com/mla/herramientas/aplicaciones 
        // Brasil: https://www.mercadopago.com/mlb/ferramentas/aplicacoes
        // Mexico: https://www.mercadopago.com/mlm/herramientas/aplicaciones 
        // Venezuela: https://www.mercadopago.com/mlv/herramientas/aplicaciones 
        $mp = new MP("CLIENT_ID", "CLIENT_SECRET");

        // Sets the filters you want
        $filters = array(
            "installments" => 12,
            "reason" => "product_name",
            "operation_type" => "regular_payment"
        );

        // Search payment data according to filters
        $searchResult = $mp->search_payment($filters);

        // Show payment information
        ?>
        <table border='1'>
            <tr><th>id</th><th>external_reference</th><th>status</th></tr>
            <?php
            foreach ($searchResult["response"]["results"] as $payment) {
                ?>
                <tr>
                    <td><?php echo $payment["collection"]["id"]; ?></td>
                    <td><?php echo $payment["collection"]["external_reference"]; ?></td>
                    <td><?php echo $payment["collection"]["status"]; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </body>
</html>

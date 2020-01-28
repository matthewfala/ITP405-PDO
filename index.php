<?php

// Add extension by uncommenting mySqlite in .ini file
// php -S localhost:3000
$pdo = new PDO('sqlite:chinook.db');
$sql = 'SELECT InvoiceId,
            InvoiceDate,
            Total,
            customers.FirstName as CustomerFirstName,
            customers.LastName as CustomerLastName
            FROM invoices
            INNER JOIN customers
            ON invoices.CustomerID = customers.CustomerID
            ';

// if search is in the url
if (isset($_GET['search'])) {
    $sql = $sql . 'WHERE customers.FirstName LIKE ?';
}

$statement = $pdo->prepare($sql);

// Parameter binding
if (isset($_GET['search'])) {
    $boundSearchParam = '%' . $_GET['search'] . '%';
    $statement->bindParam(1, $boundSearchParam); // bind to the first ?
}


$statement->execute();

//$invoices = $statement->fetchAll(); // Array of arrays
$invoices = $statement->fetchAll(PDO::FETCH_OBJ); // Array of objects

//var_dump($invoices);

?>

<form action="index.php" method="get">
    <input type="text" name="search" placeholder="Search..."
    value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''?>" >
    <button type="submit">
        Search
    </button>
</form>

<table>
    <thead>
        <tr>ID</tr>
        <tr>Data</tr>
        <tr>Total</tr>
        <tr>Customer</tr>
    </thead>
    <tbody>

        <?php foreach($invoices as $invoice) :

            /*
            foreach($invoices as $invoice) {
                // Not maintainable
                echo "<div>$invoice</div>";
            }
            */

            ?>

            <tr>
               

                <td>
                    <?php echo $invoice->InvoiceId ?>
                </td>
                <td>
                    <?php echo $invoice->InvoiceDate ?>
                </td>
                <td>
                    <?php echo $invoice->Total ?>
                </td>
                <td>
                    <?php echo $invoice->CustomerFirstName . " " . $invoice->CustomerLastName ?>
                </td>
                <td>
                    <a href="invoice-details.php?invoice=<?php echo $invoice->InvoiceId ?>">
                        Details
                    </a>
            </tr>

        <?php endforeach ?>
        <?php if (count($invoices) === 0) : ?>
            <tr>
                <td colspan="4">
                    No results
                </td>
            </tr>
        <?php endif ?>
    </tbody>
</table>

<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>
<?php include '../../Includes/tenant_context.php'; ?>


<?php

$tenant_id = getCurrentTenantId($con);

if (isset($_POST['do']) && $_POST['do'] == "Add") {
    $category_name = test_input($_POST['category_name']);

    // Check manually with tenant_id because checkItem function is not tenant aware (yet) 
    $stmtCheck = $con->prepare("SELECT * FROM service_categories WHERE category_name = ? AND tenant_id = ?");
    $stmtCheck->execute(array($category_name, $tenant_id));
    $checkItem = $stmtCheck->rowCount();

    if ($checkItem != 0) {
        $data['alert'] = "Warning";
        $data['message'] = "This category name already exists!";
        echo json_encode($data);
        exit();
    } elseif ($checkItem == 0) {
        //Insert into the database
        $stmt = $con->prepare("insert into service_categories(category_name, tenant_id) values(?, ?) ");
        $stmt->execute(array($category_name, $tenant_id));

        $data['alert'] = "Success";
        $data['message'] = "The new category has been inserted successfully !";
        echo json_encode($data);
        exit();
    }

}

if (isset($_POST['action']) && $_POST['action'] == "Delete") {
    $category_id = $_POST['category_id'];

    try {
        $con->beginTransaction();

        $stmt_services = $con->prepare("select service_id from services where category_id = ? AND tenant_id = ?");
        $stmt_services->execute(array($category_id, $tenant_id));
        $services = $stmt_services->fetchAll();
        $services_count = $stmt_services->rowCount();

        if ($services_count > 0) {
            $stmt_service_uncategorized = $con->prepare("select category_id from service_categories where LOWER(category_name) = ? AND tenant_id = ?");
            $stmt_service_uncategorized->execute(array("uncategorized", $tenant_id));
            $uncategorized_id = $stmt_service_uncategorized->fetch();

            foreach ($services as $service) {
                $stmt_update_service = $con->prepare("UPDATE services set category_id = ? where service_id = ? AND tenant_id = ?");
                $stmt_update_service->execute(array($uncategorized_id["category_id"], $service["service_id"], $tenant_id));
            }
        }

        $stmt = $con->prepare("DELETE from service_categories where category_id = ? AND tenant_id = ?");
        $stmt->execute(array($category_id, $tenant_id));
        $con->commit();
        $data['alert'] = "Success";
        $data['message'] = "The new category has been inserted successfully !";
        echo json_encode($data);
        exit();


    } catch (Exception $exp) {
        echo $exp->getMessage();
        $con->rollBack();
        $data['alert'] = "Warning";
        $data['message'] = $exp->getMessage();
        echo json_encode($data);
        exit();
    }

}

if (isset($_POST['action']) && $_POST['action'] == "Edit") {
    $category_id = $_POST['category_id'];
    $category_name = test_input($_POST['category_name']);

    // Check manually with tenant_id
    $stmtCheck = $con->prepare("SELECT * FROM service_categories WHERE category_name = ? AND tenant_id = ?");
    $stmtCheck->execute(array($category_name, $tenant_id));
    $checkItem = $stmtCheck->rowCount();

    if ($checkItem != 0) {
        $data['alert'] = "Warning";
        $data['message'] = "This category name already exists!";
        echo json_encode($data);
        exit();
    } elseif ($checkItem == 0) {

        try {
            $stmt = $con->prepare("UPDATE service_categories set category_name = ? where category_id = ? AND tenant_id = ?");
            $stmt->execute(array($category_name, $category_id, $tenant_id));

            $data['alert'] = "Success";
            $data['message'] = "Category name has been updated successfully!";
            echo json_encode($data);
            exit();
        } catch (Exception $e) {
            $data['alert'] = "Warning";
            $data['message'] = $e->getMessage();
            echo json_encode($data);
            exit();
        }


    }
}

?>
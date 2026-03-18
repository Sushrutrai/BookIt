<?php
$json=file_get_contents('php://input');
$data=json_decode($json,true);

$id=$data['id'];
$eid=$data['eid'];
$status=$data['liked'];

require_once __DIR__ . '/../bootstrap.php';

try{
    if($status===false){
    $query=$connection->prepare("Delete from bookmarks where eid=? && id=?");
    $query->bind_param('ii',$eid,$id);
    $query->execute();
    }
    else{
        $sql="Insert into bookmarks(eid,id) values(?,?)";
    $query=$connection->prepare($sql);
    $query->bind_param('ii',$eid,$id);
    $query->execute();
    }
    echo json_encode(['success'=>true,'message'=>'status updated']);
}catch(Exception $e){
    echo json_encode(['success'=>false,'message'=> $e->getMessage()]);
}


?>
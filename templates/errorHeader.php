<?php        

//$statusHeader = 'HTTP/1.1 ' . $this->getStatusCode() . '  ' .
//                        $this->getStatusCodeMessage($this->getStatusCode());
//header($statusHeader);
//header('Content-type: ' . $this->getContentType()); 

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
    <head>
        <title><?php echo $this->statusCode; ?> : <?php echo $this->statusCodeMessage; ?></title>
    </head>
    <body>
        
    
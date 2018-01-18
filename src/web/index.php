<?php

require_once(__DIR__ . '/bootstrap.php');
//
//$ch = curl_init();
//
//curl_setopt($ch, CURLOPT_URL, "http://rk.local/recruitMe/job");
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($ch, CURLOPT_HEADER, FALSE);
//
//curl_setopt($ch, CURLOPT_POST, TRUE);
//
//curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(    array(
//    'title'       => 'C# Software Engineer',
//    'category'    => 'Technology',
//    'description' => '<p><strong>Description</strong></p><p>Ideal candidates will have a background in computer science or software engineering, will have the experience to design and develop great software in both C# and other languages, and an appetite to make an impact on the company at large.&nbsp;</p><p><strong>Responsibilities</strong></p><ul><li>Design, develop and maintain high quality software.</li><li>Solve complex problems – find the best and most efficient solution, design model or design process.</li><li>Develop core operational infrastructure including trading, portfolio, algorithmic execution, accounting and risk applications.</li><li>Facilitate the growth of the firm’s evolving business by creating robust and scalable technical solutions.</li></ul><p><strong>&nbsp;</strong></p><p><strong>Requirements</strong></p><ul><li>Strong programming skills in C#.</li><li>Knowledge of software engineering practices, for example testing frameworks, methods such as Scrum, Agile, design patterns, dependency injection.</li><li>Strong communication and prioritisation skills.</li></ul><strong><br></strong><p><strong>Desirable experience</strong></p><ul><li>SQL development skills</li></ul><br><br>',
//    'location'    => 'London',
//)));
//
//curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//    "Content-Type: application/json"
//));
//
//$response = curl_exec($ch);
//$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//curl_close($ch);
//
//var_dump($httpCode, $response);
//
//die();
try {

    // simple logic to call the correct app dispatcher based on the URL
    $component = \Rk\Routing\Router::getActionFromRequest('Front');
    $response = $component->execute();

    $response->send();

} catch (Throwable $e) {
    // generic error management
    http_response_code(500);
    echo '<pre>Sorry, an error has occurred.</pre>';

    if (\Rk\Config::isDebug()) {
        if (!empty($e->xdebug_message)) {
            echo '<table>' . $e->xdebug_message . '</table>';
        } else {
            var_dump($e);
        }
    }
}

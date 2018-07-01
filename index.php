<?php

use PhpExtended\Mega\Mega;
use PhpExtended\Mega\MegaNode;
use PhpExtended\Mega\MegaException;
use PhpExtended\Mega\MegaNodeId;

require __DIR__ . '/vendor/autoload.php';

// Initialize env configuration.
$dotEnv = new Dotenv\Dotenv(__DIR__);
$dotEnv->load();

$mega = new Mega(getenv('MEGA_FOLDER_URL'), '', '');

/**
 * Downloads a MegaNode containing a file.
 *
 * @param MegaNode $megaNode
 * @throws MegaException
 */
function download(MegaNode $megaNode)
{
    // TODO: change to initialized static singleton.
    global $mega;

    $fileDownload = \Apfelbox\FileDownload\FileDownload::createFromString($mega->downloadFile($megaNode));
    $fileDownload->sendDownload($megaNode->getAttributes()->getName());
    die();
}

/**
 * Retrieves a recursive file list from a MegaNode.
 *
 * @param MegaNode $megaNode
 * @param array $files
 */
function recursiveFileList(MegaNode $megaNode, array &$files)
{
    // TODO: change to initialized static singleton.
    global $mega;

    try {
        $childMegaNodes = $mega->getChildren($megaNode);
    } catch (MegaException $e) {
        // The folder is empty.
        return;
    }

    foreach ($childMegaNodes as $childMegaNode) {
        /*
        var_dump($childMegaNode->getAttributes()->getName());
        var_dump($childMegaNode->getNodeSize());
        var_dump('is size null: ' . $childMegaNode->getNodeSize() === null ? 'true' : 'false');
        var_dump($childMegaNode);
        var_dump('-----------------');
        */

        if ($childMegaNode->getNodeSize() === null) {
            // Is a directory.
            recursiveFileList($childMegaNode, $files);
        } else {
            // Is a file.
            $files[$childMegaNode->getNodeId()->getValue()] = $childMegaNode->getAttributes()->getName();
        }
    }
}

$rootNode = $mega->getRootNodeInfo();

/**
 * Test method to retrieve all files in the root node.
 */
/*
$files = [];
recursiveFileList($rootNode, $files);

echo "<pre>";
foreach ($files as $file) {
    var_dump($file);
}
echo "</pre>";
*/

/**
 * Test method to download a file from a node id.
 */
/*
$megaNodeId = new MegaNodeId(getenv('MEGA_SAMPLE_NODE_ID'));
$testMegaNode = $mega->getFileInfo($megaNodeId);
download($testMegaNode);
*/
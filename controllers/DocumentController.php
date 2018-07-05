<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class DocumentController
{
    private static $instance;
    public static function shared()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function getCount()
    {
        return DocumentHelper::shared()->getCount();
    }

    public function getDocuments($page_index, $page_size)
    {
        $page_index = ($page_index * $page_size);
        $documents = DocumentHelper::shared()->getDocuments($page_index, $page_size);
        return $documents;
    }

    public function getDocumentById($id)
    {
        $document = DocumentHelper::shared()->getDocumentById($id);
        return $document;
    }

    public function getDocumentByKey($key, $id = NULL)
    {
        $document = DocumentHelper::shared()->getDocumentByKey($key, $id);
        return $document;
    }

    public function deleteDocument($id)
    {
        return DocumentHelper::shared()->deleteDocument($id);
    }

    public function createDocument($key, $name, $author)
    {
        return DocumentHelper::shared()->createDocument($key, $name, $author);
    }

    public function updateDocument($id, $key, $name, $author)
    {
        DocumentHelper::shared()->updateDocument($id, $key, $name, $author);
    }
}
?>

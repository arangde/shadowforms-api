<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class DocumentHelper
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
        return DbHelper::shared()->count('fnf_documents');
    }

    public function getDocuments($page_index, $page_size)
    {
        $query = "SELECT fnf_documents.document_id AS id, fnf_documents.document_key AS 'key', fnf_documents.document_name AS name, fnf_documents.document_author AS author, CONCAT_WS('', '" . DOCUMENT_BASE_URL . "', CONCAT(document_id, '-', document_created), '.pdf') AS url, fnf_documents.document_created AS created, fnf_documents.document_modified AS modified FROM fnf_documents LIMIT " . $page_index . "," . $page_size;
		$documents = DbHelper::shared()->query($query)->fetchAll(PDO::FETCH_ASSOC);

        return $documents;
    }

    public function getDocumentById($id)
    {
        $query = "SELECT fnf_documents.document_id AS id, fnf_documents.document_key AS 'key', fnf_documents.document_name AS name, fnf_documents.document_author AS author, CONCAT_WS('', '" . DOCUMENT_BASE_URL . "', CONCAT(document_id, '-', document_created), '.pdf') AS url, fnf_documents.document_created AS created, fnf_documents.document_modified AS modified FROM fnf_documents WHERE fnf_documents.document_id = " . $id;
		$documents = DbHelper::shared()->query($query)->fetchAll(PDO::FETCH_ASSOC);

        if ((sizeof($documents) > 0) && (sizeof($documents[0]) > 0)) {
			return $documents[0];
		} else {
			return NULL;
		}
    }

    public function getDocumentByKey($key, $id = NULL)
    {
        $documents = array();
        if (is_null($id)) {
            $query = "SELECT fnf_documents.document_id AS id, fnf_documents.document_key AS 'key', fnf_documents.document_name AS name, fnf_documents.document_author AS author, CONCAT_WS('', '" . DOCUMENT_BASE_URL . "', CONCAT(document_id, '-', document_created), '.pdf') AS url, fnf_documents.document_created AS created, fnf_documents.document_modified AS modified FROM fnf_documents WHERE fnf_documents.document_key = '" . $key . "'";
            $documents = DbHelper::shared()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $query = "SELECT fnf_documents.document_id AS id, fnf_documents.document_key AS 'key', fnf_documents.document_name AS name, fnf_documents.document_author AS author, CONCAT_WS('', '" . DOCUMENT_BASE_URL . "', CONCAT(document_id, '-', document_created), '.pdf') AS url, fnf_documents.document_created AS created, fnf_documents.document_modified AS modified FROM fnf_documents WHERE fnf_documents.document_key = '" . $key . "' AND fnf_documents.document_id <> " . $id;
            $documents = DbHelper::shared()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        }
        
        if ((sizeof($documents) > 0) && (sizeof($documents[0]) > 0)) {
			return $documents[0];
		} else {
			return NULL;
		}
    }

    public function deleteDocument($id)
    {
        DbHelper::shared()->delete('fnf_documents', array('AND' => array('document_id' => $id)));
    }

    public function createDocument($key, $name, $author)
    {
        DbHelper::shared()->insert('fnf_documents', array('document_key' => $key, 'document_name' => $name, 'document_author' => $author, 'document_created' => time(), 'document_modified' => time()));
		$id = DbHelper::shared()->id();

        return $id;
    }

    public function updateDocument($id, $key, $name, $author)
    {
        DbHelper::shared()->update('fnf_documents', array('document_key' => $key, 'document_name' => $name, 'document_author' => $author, 'document_modified' => time()), array('AND' => array('document_id' => $id)));
    }
}
?>

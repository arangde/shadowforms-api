<?php
require_once PROJECT_ROOT . DIRECTORY_SEPARATOR . 'autoloader.php';

class DocumentInterface
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

    public function get()
    {
        return function ($request, $response, $args) {
            $output = array();
            $response = $response->withStatus(200);
            $response = $response->withHeader('Content-Type', 'application/json');
			
			$total = DocumentController::shared()->getCount();
            $documents = DocumentController::shared()->getDocuments((isset($args['page_index']) ? $args['page_index'] : 0), (isset($args['page_size']) ? $args['page_size'] : 5));
            if (sizeof($documents) > 0) {
                $output["response_status"] = true;
                $output["response_data"] = ["documents" => $documents, "count" => $total];
                $output["response_message"]	= "";
            } else {
                $output["response_status"] = false;
                $output["response_data"] = [];
                $output["response_message"]	= "No document found.";
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }

    public function getById()
    {
        return function ($request, $response, $args) {
            $output = array();
            $response = $response->withStatus(200);
            $response = $response->withHeader('Content-Type', 'application/json');

            $document = DocumentController::shared()->getDocumentById($args['id']);
            if (sizeof($document) > 0) {
                $contents = ContentController::shared()->getContentsByDocumentId($document['id']);
                $document["contents"] = $contents;

                $output["response_status"] = true;
                $output["response_data"] = ["document" => $document];
            } else {
                $output["response_status"] = false;
                $output["response_message"]	= "No document found.";
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }

    public function getByKey()
    {
        return function ($request, $response, $args) {
            $output = array();
            $response = $response->withStatus(200);
            $response = $response->withHeader('Content-Type', 'application/json');

            $document = DocumentController::shared()->getDocumentByKey($args['key']);
            if (sizeof($document) > 0) {
                $contents = ContentController::shared()->getContentsByDocumentId($document['id']);
                $document["contents"] = $contents;
                
                $output["response_status"] = true;
                $output["response_data"] = ["document" => $document];
            } else {
                $output["response_status"] = false;
                $output["response_message"]	= "No document found.";
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }

    public function delete()
    {
        return function ($request, $response, $args) {
            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');

            if (isset($args['id'])) {
				$file_name = "";
                $document = DocumentController::shared()->getDocumentById($args['id']);
				if (sizeof($document) > 0) {
					$file_name = $document['id'] . "-" . $document['created'] . ".pdf";
					if (file_exists(DOCUMENT_BASE_PATH .$file_name)) {
						unlink(DOCUMENT_BASE_PATH . $file_name);
					}

                    DocumentController::shared()->deleteDocument($document['id']);
				}

                $output["response_status"] = true;
                $output["response_message"]	= "Document deleted successfully.";
                $response = $response->withStatus(200);
            } else {
                $body = $request->getBody();
                $input = json_decode($body);

                $errors = array();
                if (is_array($input) == false) {
                    array_push($errors, array('param' => 'identifiers', 'msg' => 'Please enter document identifiers.'));
                } else if (sizeof($input) == 0) {
                    array_push($errors, array('param' => 'identifiers', 'msg' => 'Please enter document identifiers.'));
                }

                $output = array();
                if (sizeof($errors) > 0) {
                    $output["response_status"] = false;
                    $output["response_message"]	= "Please enter required fields.";
                    $output["response_data"] = ["error_info" => $errors];

                    $response = $response->withStatus(400);
                } else {
					foreach ($input as $id) {
						$file_name = "";
						$document = DocumentController::shared()->getDocumentById($id);
						if (sizeof($document) > 0) {
                            $file_name = $document['id'] . "-" . $document['created'] . ".pdf";
                            if (file_exists(DOCUMENT_BASE_PATH .$file_name)) {
                                unlink(DOCUMENT_BASE_PATH . $file_name);
                            }
						}
					}

                    DocumentController::shared()->deleteDocument($input);

                    $output["response_status"] = true;
                    $output["response_message"]	= "Documents deleted successfully.";
                    $response = $response->withStatus(200);
                }
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }

    public function create()
    {
        return function ($request, $response, $args) {
            $files = $request->getUploadedFiles();
            $body = $request->getParsedBody();
            
            $errors = array();
			if (empty($files['pdf'])) {
                array_push($errors, array('param' => 'pdf', 'msg' => 'Please provide pdf file.'));
            } else if ($files['pdf']->getClientMediaType() != "application/pdf") {
                array_push($errors, array('param' => 'pdf', 'msg' => 'Please provide a valid pdf file.'));
            }
			
            if (empty($body['key'])) {
                array_push($errors, array('param' => 'key', 'msg' => 'Please provide document key.'));
            }

            if (empty($body['name'])) {
                array_push($errors, array('param' => 'name', 'msg' => 'Please provide document name.'));
            }

            if (empty($body['author'])) {
                array_push($errors, array('param' => 'author', 'msg' => 'Please provide document author name.'));
            }

            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');
            if (sizeof($errors) > 0) {
                $output["response_status"] = false;
                $output["response_message"]	= "Please enter required fields.";
                $output["response_data"] = ["error_info" => $errors];

                $response = $response->withStatus(400);
            } else {
                $document = DocumentController::shared()->getDocumentByKey($body['key']);
                if (is_null($document)) {
                    $id = DocumentController::shared()->createDocument($body['key'], $body['name'], $body['author']);
                    $document = DocumentController::shared()->getDocumentById($id);
                    if (is_null($document)) {
                        $output["response_status"] = false;
                        $output["response_message"]	= "Failed to create new document.";
                        $output["response_data"] = [];

                        $response = $response->withStatus(400);
                    } else {
                        $file_name = $document['id'] . "-" . $document['created'] . ".pdf";
                        move_uploaded_file($files['pdf']->file, DOCUMENT_BASE_PATH . $file_name);

                        $output["response_status"] = true;
                        $output["response_message"]	= "Document created successfully.";
                        $output["response_data"] = [];

                        $response = $response->withStatus(200);
                    }
                } else {
                    $output["response_status"] = false;
                    $output["response_message"]	= "Document with given key already exist.";
                    $output["response_data"] = [];

                    $response = $response->withStatus(400);
                }
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }

    public function update()
    {
        return function ($request, $response, $args) {
            $body = $request->getBody();
            $input = json_decode($body);

            $errors = array();
			if (empty($input->key)) {
                array_push($errors, array('param' => 'key', 'msg' => 'Please enter document key.'));
            }

            if (empty($input->name)) {
                array_push($errors, array('param' => 'name', 'msg' => 'Please enter document name.'));
            }

            if (empty($input->author)) {
                array_push($errors, array('param' => 'author', 'msg' => 'Please enter document author name.'));
            }

            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');
            if (sizeof($errors) > 0) {
                $output["response_status"] = false;
                $output["response_message"]	= "Please enter required fields.";
                $output["response_data"] = ["error_info" => $errors];

                $response = $response->withStatus(400);
            } else {
                $document = DocumentController::shared()->getDocumentByKey($input->key, $args['id']);
                if (is_null($document)) {
                    $document = DocumentController::shared()->getDocumentById($args['id']);
                    if (is_null($document)) {
                        $output["response_status"] = false;
                        $output["response_message"]	= "No such document found.";
                        $output["response_data"] = [];

                        $response = $response->withStatus(400);
                    } else {
                        DocumentController::shared()->updateDocument($args['id'], $input->key, $input->name, $input->author);
                        $output["response_status"] = true;
                        $output["response_message"]	= "Document updated successfully.";
                        $output["response_data"] = [];

                        $response = $response->withStatus(200);
                    }
                } else {
                    $output["response_status"] = false;
                    $output["response_message"]	= "Document with given key already exist.";
                    $output["response_data"] = [];

                    $response = $response->withStatus(400);
                }
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }
    
    public function modify()
    {
        return function ($request, $response, $args) {
            $body = $request->getBody();
            $contents = json_decode($body);

            $errors = array();
			if (is_array($contents) == false) {
                array_push($errors, array('param' => 'contents', 'msg' => 'Please provide document contents.'));
            } else {
                foreach ($contents as $content) {
                    if (isset($content->id) == true) {
                        if (is_numeric($content->id) == false) {
                            array_push($errors, array('param' => 'id', 'msg' => 'Please provide content id.'));
                        }
                    }

                    if (empty($content->name)) {
                        array_push($errors, array('param' => 'name', 'msg' => 'Please provide content name.'));
                    }

                    if (is_numeric($content->type) == false) {
                        array_push($errors, array('param' => 'type', 'msg' => 'Please provide content type.'));
                    } else {
                        $types = array(0, 1, 2, 3, 4, 5, 6, 7);
                        if (in_array($content->type, $types, true) == false) {
                            array_push($errors, array('param' => 'type', 'msg' => 'Please provide valid content type.'));
                        }
                    }

                    if (empty($content->sx)) {
                        array_push($errors, array('param' => 'sx', 'msg' => 'Please provide content x scale.'));
                    }

                    if (empty($content->sy)) {
                        array_push($errors, array('param' => 'sy', 'msg' => 'Please provide content y scale.'));
                    }

                    if (empty($content->x)) {
                        array_push($errors, array('param' => 'x', 'msg' => 'Please provide content x position.'));
                    }

                    if (empty($content->y)) {
                        array_push($errors, array('param' => 'y', 'msg' => 'Please provide content y position.'));
                    }

                    if (empty($content->w)) {
                        array_push($errors, array('param' => 'w', 'msg' => 'Please provide content width.'));
                    }

                    if (empty($content->h)) {
                        array_push($errors, array('param' => 'h', 'msg' => 'Please provide content height.'));
                    }

                    if (is_numeric($content->page_number) == false) {
                        array_push($errors, array('param' => 'page_number', 'msg' => 'Please provide page number of content.'));
                    } else if ($content->page_number <= 0) {
                        array_push($errors, array('param' => 'page_number', 'msg' => 'Please provide valid page number of content.'));
                    }
                }
            }

            $output = array();
            $response = $response->withHeader('Content-Type', 'application/json');
            if (sizeof($errors) > 0) {
                $output["response_status"] = false;
                $output["response_message"]	= "Please enter required fields.";
                $output["response_data"] = ["error_info" => $errors];

                $response = $response->withStatus(400);
            } else {
                $document = DocumentController::shared()->getDocumentById($args['id']);
                if (is_null($document)) {
                    $output["response_status"] = false;
                    $output["response_message"]	= "No such document found.";
                    $output["response_data"] = [];

                    $response = $response->withStatus(400);
                } else {
                    if (sizeof($contents) > 0) {
                        $existing_contents = array_filter($contents, function ($item) { return (isset($item->id) == true); });
                        if (sizeof($existing_contents) > 0) {
                            foreach ($existing_contents as $content) {
                                $content_description = "";
                                if (empty($content->description) == false) {
                                    $content_description = $content->description;
                                }
                                ContentController::shared()->updateContent($content->id, $args['id'], $content->name, $content_description, $content->type, $content->sx, $content->sy, $content->x, $content->y, $content->w, $content->h, $content->page_number);
                            }

                            // Remove contetns which are not in existing content list but may exist in database.
                            $content_ids = array_map(function ($item) { return ($item->id); }, $existing_contents);
                            ContentController::shared()->deleteContentsNotIn($content_ids, $args['id']);
                        } else {
                            ContentController::shared()->deleteContentsByDocumentId($args['id']);
                        }

                        $new_contents = array_filter($contents, function ($item) { return (isset($item->id) == false); });
                        if (sizeof($new_contents) > 0) {
                            foreach ($new_contents as $content) {
                                $content_description = "";
                                if (empty($content->description) == false) {
                                    $content_description = $content->description;
                                }
                                ContentController::shared()->createContent($args['id'], $content->name, $content_description, $content->type, $content->sx, $content->sy, $content->x, $content->y, $content->w, $content->h, $content->page_number);
                            }
                        }
                    } else {
                        ContentController::shared()->deleteContentsByDocumentId($args['id']);
                    }

                    $output["response_status"] = true;
                    $output["response_message"]	= "Document contents modified successfully.";
                    $output["response_data"] = [];

                    $response = $response->withStatus(200);
                }
            }

            $response->write(json_encode($output, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK));
            return $response;
        };
    }
}
?>

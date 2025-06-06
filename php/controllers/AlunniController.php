<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AlunniController
{
  public function index(Request $request, Response $response, $args){
    $db = Db::getIstance();
    $result = $db->select("alunni");
    //                          Serializzazione in json 
    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
  }

  public function search(Request $request, Response $response, $args) {
    $db = Db::getIstance();

    $queryParams = $request->getQueryParams();
    $nome = $queryParams['nome'] ?? null;
    $cognome = $queryParams['cognome'] ?? null;
    $sort = $queryParams['sort'] ?? null;
    $sort_per = $queryParams['sort_per'] ?? 'id';  // Default a 'id' se non specificato
    $sort_dir = isset($queryParams['sort_dir']) ? strtoupper($queryParams['sort_dir']) : 'ASC';

    // Validazione parametri di ordinamento
    $sort_dir = ($sort_dir === 'DESC') ? 'DESC' : 'ASC';
    $allowed_columns = ['id', 'nome', 'cognome', 'eta'];
    $sort_per = in_array($sort_per, $allowed_columns) ? $sort_per : 'id';

    // Costruzione della condizione WHERE
    $conditions = [];
    $params = [];

    if (!empty($nome)) {
        $conditions[] = "nome LIKE '%" . $this->real_escape_string($nome) . "%'";
    }

    if (!empty($cognome)) {
        $conditions[] = "cognome LIKE '%" . $this->real_escape_string($cognome) . "%'";
    }

    $where = !empty($conditions) ? implode(' AND ', $conditions) : '1';

    // Aggiungi ordinamento se richiesto
    if ($sort) {
        $where .= " ORDER BY $sort_per $sort_dir";
    }

    // Esegui la query usando il metodo sort()
    $result = $db->sort('alunni', $where);

    $response->getBody()->write(json_encode($result));
    return $response->withHeader("Content-type", "application/json")->withStatus(200);
}

  public function show(Request $request, Response $response, $args){
     // $queryParams = $request->getQueryParams();
     // var_dump($queryParams);
     // exit;
     //curl http://localhost:8080/alunni/1
     $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
     $stmt = $mysqli_connection->prepare("SELECT * FROM alunni WHERE id = ?");
     $stmt->bind_param("i", $args['id']);
     $stmt->execute();
     $result = $stmt->get_result();
     $results = $result->fetch_all(MYSQLI_ASSOC);
     //                          Serializzazione in json 
     return $response->withHeader("Content-type", "application/json")->withStatus(200);
     $response->getBody()->write(json_encode($results));
  }

  public function create(Request $request, Response $response) {
    //curl -X POST http://localhost:8080/alunni -H "Content-Type: application/json" -d '{"nome": "Dario","cognome": "Chen"}'
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    // Recupera i dati dal body della richiesta (JSON)
    $data = json_decode($request->getBody()->getContents(), true);
    
    // Prepara la query SQL (usa backtick per i nomi di tabelle/colonne)
    $stmt = $mysqli_connection->prepare("INSERT INTO alunni (nome, cognome) VALUES (?, ?)");
    $stmt->bind_param("ss", $data['nome'], $data['cognome']);
    $stmt->execute();
    
    // Chiudi lo statement
    $stmt->close();
    
    //Risposta di successo
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'message' => 'Alunno creato con successo',
        'id' => $mysqli_connection->insert_id
    ]));
    
    return $response->withHeader("Content-type", "application/json")->withStatus(201); 
  }

  public function update(Request $request, Response $response, $args) {
    //curl -X PUT http://localhost:8080/alunni/3 -H "Content-Type: application/json" -d '{"nome": "Ruji"}'
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    // Recupera i dati dal body della richiesta (JSON)
    $data = json_decode($request->getBody()->getContents(), true);
    
    // Prepara la query SQL
    $stmt = $mysqli_connection->prepare("UPDATE alunni SET nome = ? WHERE id = ?");
    $stmt->bind_param("ss", $data['nome'], $args['id']);
    $stmt->execute();
    
    // Chiudi lo statement
    $stmt->close();
    
    //Risposta di successo
    $response->getBody()->write(json_encode([
      'status' => 'success',
      'message' => 'Alunno Aggiornato con successo',
      'id' => $mysqli_connection->insert_id
    ]));
    
    return $response->withHeader("Content-type", "application/json")->withStatus(201); 
  }

  public function destroy(Request $request, Response $response, $args) {
    $mysqli_connection = new MySQLi('my_mariadb', 'root', 'ciccio', 'scuola');
    
    // Prepara la query SQL
    $stmt = $mysqli_connection->prepare("DELETE FROM alunni WHERE id = ?");
    $stmt->bind_param("i", $args['id']);
    $stmt->execute();
    // Chiudi lo statement
    $stmt->close();
    
    //Risposta di successo
    $response->getBody()->write(json_encode([
      'status' => 'success',
      'message' => 'Alunno Eliminato con successo',
      'id' => $args['id'],  
      'affected_rows' => $affectedRows
    ]));
    
    return $response->withHeader("Content-type", "application/json")->withStatus(201); 
  }

}

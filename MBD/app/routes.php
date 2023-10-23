<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;


return function (App $app) {

    // get 
    // harga
    $app->get('/harga', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM harga');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });
    // hotel
    $app->get('/hotel', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM hotel');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //rating_review
    $app->get('/rating_review', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM rating_review');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    //user
    $app->get('/user', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('SELECT * FROM user');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get hotel by id
    $app->get('/hotel/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM hotel WHERE id=?');
        $query->execute([$args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get harga by id
    $app->get('/harga/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM harga WHERE id=?');
        $query->execute([$args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get rating_review by id
    $app->get('/rating_review/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM rating_review WHERE id=?');
        $query->execute([$args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get user by id
    $app->get('/user/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('SELECT * FROM user WHERE id=?');
        $query->execute([$args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    //tabel hotel
    //post hotels
    $app->post('/hotels', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $namaHotel = $parsedBody["nama_hotel"];
        $alamatHotel = $parsedBody["alamat_hotel"];
        $namaKota = $parsedBody["nama_kota"];

    
        $db = $this->get(PDO::class);
    
        $query = $db->prepare('CALL tambah_hotel(?, ?, ?)');
        $query->execute([$namaHotel, $alamatHotel, $namaKota]);
    
        $result = $query->fetch(PDO::FETCH_ASSOC);
    
        $response->getBody()->write(json_encode($result));
    
        return $response->withHeader("Content-Type", "application/json");
    });

    //update hotel
    $app->put('/hotels/{id}', function (Request $request, Response $response, $args) {
        $hotelId = $args['id'];
        $db = $this->get(PDO::class);
    
        // Mendapatkan data yang akan diupdate dari body request
        $parsedBody = $request->getParsedBody();
        $newNamaHotel = $parsedBody["nama_hotel"];
        $newAlamatHotel = $parsedBody["alamat_hotel"];
        $newNamaKota = $parsedBody["nama_kota"];
    
        try {
            // Update data dalam tabel "hotel" berdasarkan ID
            $query = $db->prepare('UPDATE hotel SET nama_hotel = ?, alamat_hotel = ?, nama_kota = ? WHERE id = ?');
            $query->execute([$newNamaHotel, $newAlamatHotel, $newNamaKota, $hotelId]);
    
            $response->getBody()->write(json_encode([
                'message' => 'Hotel dengan ID ' . $hotelId . ' telah diperbarui'
            ]));
    
            return $response->withHeader("Content-Type", "application/json");
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]));
    
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });
    
    
    // Delete
    $app->delete('/hotels/{id}', function (Request $request, Response $response, $args) {
        $currentId = $args['id'];
    
        $db = $this->get(PDO::class);
    
        $query = $db->prepare('CALL hapus_hotel(?)');
        $query->bindParam(1, $currentId, PDO::PARAM_INT);
        $query->execute();
    
        $affectedRows = $query->rowCount();
    
        if ($affectedRows > 0) {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Hotel dengan ID ' . $currentId . ' telah dihapus'
                ]
            ));
        } else {
            $response = $response->withStatus(404); // Atur status 404 jika data tidak ditemukan
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Hotel dengan ID ' . $currentId . ' tidak ditemukan'
                ]
            ));
        }
    
        return $response->withHeader("Content-Type", "application/json");
    });

    ////////////////////////////////////////////////////////////////////////////////////////////////
    // tabel harga
    //post
    $app->post('/harga', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $kategoriKamar = $parsedBody["kategori_kamar"];
        $hargaKamar = $parsedBody["harga_kamar"];
        $hotelId = $parsedBody["hotel_id"];
    
        $db = $this->get(PDO::class);
    
        $query = $db->prepare('CALL CreateHarga(?, ?, ?)');
        $query->execute([$kategoriKamar, $hargaKamar, $hotelId]);
    
        return $response->withHeader("Content-Type", "application/json");
    });
    //update harga
    $app->put('/harga/{id}', function (Request $request, Response $response, $args) {
        $hargaId = $args['id'];
        $parsedBody = $request->getParsedBody();
    
        $kategoriKamar = $parsedBody["kategori_kamar"];
        $hargaKamar = $parsedBody["harga_kamar"];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure UpdateHarga
        $query = $db->prepare('CALL UpdateHarga(?, ?, ?)');
        $query->execute([$hargaId, $kategoriKamar, $hargaKamar]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data harga dengan ID ' . $hargaId . ' telah diperbarui'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal memperbarui harga'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    //delete harga
    $app->delete('/harga/{id}', function (Request $request, Response $response, $args) {
        $hargaId = $args['id'];
        $db = $this->get(PDO::class);
    
        try {
            // Call the stored procedure to delete data in the "harga" table based on the ID
            $query = $db->prepare('CALL DeleteHarga(?)');
            $query->bindParam(1, $hargaId, PDO::PARAM_INT);
            $query->execute();
    
            $affectedRows = $query->rowCount();
    
            if ($affectedRows > 0) {
                $response->getBody()->write(json_encode([
                    'message' => 'Data harga dengan ID ' . $hargaId . ' telah dihapus'
                ]));
                return $response->withStatus(200)->withHeader("Content-Type", "application/json");
            } else {
                $response->getBody()->write(json_encode([
                    'message' => 'Data harga dengan ID ' . $hargaId . ' tidak ditemukan'
                ]));
                return $response->withStatus(404)->withHeader("Content-Type", "application/json");
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    ////////////////////////////////////////////////////////////////

    //rating_review
    // post
    $app->post('/rating_review', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $hotelId = $parsedBody["hotel_id"];
        $userId = $parsedBody["user_id"];
        $rating = $parsedBody["rating"];
        $reviewText = $parsedBody["review_text"];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure CreateRatingReview
        $query = $db->prepare('CALL CreateRatingReview(?, ?, ?, ?)');
        $query->execute([$hotelId, $userId, $rating, $reviewText]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode(['message' => 'Data ulasan rating telah disimpan']));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode(['error' => 'Gagal menyimpan ulasan rating']));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    //update 
    $app->put('/rating_review/{id}', function (Request $request, Response $response, $args) {
        $ratingReviewId = $args['id'];
        $parsedBody = $request->getParsedBody();
    
        $newRating = $parsedBody["rating"];
        $newReviewText = $parsedBody["review_text"];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure UpdateRatingReview
        $query = $db->prepare('CALL UpdateRatingReview(?, ?, ?)');
        $query->execute([$ratingReviewId, $newRating, $newReviewText]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data ulasan rating dengan ID ' . $ratingReviewId . ' telah diperbarui'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal memperbarui ulasan rating'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    // Delete rating
    $app->delete('/rating_review/{id}', function (Request $request, Response $response, $args) {
        $ratingReviewId = $args['id'];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure DeleteRatingReview
        $query = $db->prepare('CALL DeleteRatingReview(?)');
        $query->execute([$ratingReviewId]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data ulasan rating dengan ID ' . $ratingReviewId . ' telah dihapus'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal menghapus ulasan rating'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });
    
    // tabel user
    // tambahkan user
    $app->post('/users', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();
    
        $namaUser = $parsedBody["nama_user"];
        $email = $parsedBody["email"];
        $password = $parsedBody["password"];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure CreateUser
        $query = $db->prepare('CALL CreateUser(?, ?, ?)');
        $query->execute([$namaUser, $email, $password]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data pengguna baru telah disimpan'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal menyimpan pengguna baru'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    //update data
    $app->put('/users/{id}', function (Request $request, Response $response, $args) {
        $userId = $args['id'];
        $parsedBody = $request->getParsedBody();
    
        $newNamaUser = $parsedBody["nama_user"];
        $newEmail = $parsedBody["email"];
        $newPassword = $parsedBody["password"];
    
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure UpdateUser
        $query = $db->prepare('CALL UpdateUser(?, ?, ?, ?)');
        $query->execute([$userId, $newNamaUser, $newEmail, $newPassword]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data pengguna dengan ID ' . $userId . ' telah diperbarui'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal memperbarui pengguna'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });

    // delete user
    $app->delete('/users/{id}', function (Request $request, Response $response, $args) {
        $userId = $args['id'];
        
        $db = $this->get(PDO::class);
    
        // Panggil stored procedure DeleteUser
        $query = $db->prepare('CALL DeleteUser(?)');
        $query->execute([$userId]);
    
        // Periksa apakah stored procedure berhasil dijalankan
        if ($query) {
            $response->getBody()->write(json_encode([
                'message' => 'Data pengguna dengan ID ' . $userId . ' telah dihapus'
            ]));
            return $response->withStatus(200)->withHeader("Content-Type", "application/json");
        } else {
            $response->getBody()->write(json_encode([
                'error' => 'Gagal menghapus pengguna'
            ]));
            return $response->withStatus(500)->withHeader("Content-Type", "application/json");
        }
    });
    
};



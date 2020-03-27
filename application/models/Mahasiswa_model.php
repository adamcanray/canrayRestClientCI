<?php

// gunakna namespace
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;

// use GuzzleHttp\Exception;
// use function GuzzleHttp\json_decode;
class Mahasiswa_model extends CI_model {
    // buat attribute private agarhanya bisa diaksesdi dalam class ini saja
    private $_client;
    // simpan api key didalam varibel agar bisa digubnakan di semua method di dalam class ini
    // dan menghemat penulisan kode ulang jika sewaktu-waktu api-key nya berubah
    private $apikey = 'wpusatu';
    // buat constructor
    public function __construct()
    {
        // ambil $_client dan isi dengan membuat class Client baru 
        $this->_client = new Client([
            // url untuk request ke server dijadikan parameter, agar pemanggilannya tidak berulang setiap method nya
            'base_uri' => 'http://localhost/belajar/rest-api/wpu-rest-server/api/',
            'auth' => ['adam','canray']
        ]);
    }

    public function getAllMahasiswa()
    {
        // ambil ke database
        // return $this->db->get('mahasiswa')->result_array();
        
        // ambil ke guzzle
        // panggil Clientnya
        // $client = new Client();
        // dinonaktifkan karena sudah di jalankan di method __constructor

        // response, request ke rest-server dengan guzzle
        $response = $this->_client->request('GET', 'mahasiswa', [
            // untuk GET di Authorization memasukan username dan password
            // gunakan 'auht' untuk Authorization
            // 'auth' => ['adam', 'canray'],
            // dinon aktifkan karena auth sudah di panggil di method __constructor

            // untuk GET di params memasukan key
            // gunakan 'query' untuk GET di params
            'query' => [    
                'wpu-key' => $this->apikey
            ]
        ]);
        
        // masuk ke dalam objext untuk mengambil datavalid nya, dan ubah jsonke array assoc(parameter kedua true)
        $result = json_decode($response->getBody()->getContents(), true);

        // kembalikan data
        return $result['data'];
    }

    public function getMahasiswaById($id)
    {
        // ambil kedatabase langsung
        // return $this->db->get_where('mahasiswa', ['id' => $id])->row_array();

        // ambil ke guzzle
        // panggil Clientnya
        // $client = new Client();

        // response, request ke rest-server dengan guzzle
        $response = $this->_client->request('GET', 'mahasiswa', [
            // untuk GET di Authorization memasukan username dan password
            // gunakan 'auht' untuk Authorization
            // 'auth' => ['adam', 'canray'],

            // untuk GET di params memasukan key
            // gunakan 'query' untuk GET di params
            'query' => [
                'wpu-key' => $this->apikey,
                'id' => $id
            ]
        ]);

        // masuk ke dalam objext untuk mengambil datavalid nya, dan ubah jsonke array assoc(parameter kedua true)
        $result = json_decode($response->getBody()->getContents(), true);

        // kembalikan data
        return $result['data'][0];
    }

    public function tambahDataMahasiswa()
    {
        // tambahkan data ke database langsung
        // validasi nya menggunakan yang di CI
        $data = [
            "nama" => $this->input->post('nama', true),
            "nrp" => $this->input->post('nrp', true),
            "email" => $this->input->post('email', true),
            "jurusan" => $this->input->post('jurusan', true),
            "wpu-key" => $this->apikey
        ];

        // $this->db->insert('mahasiswa', $data);

        // tambahkan data dengan konekn ke guzzle, lewat rest-server
        $response = $this->_client->request('POST', 'mahasiswa', [
            // parameter, lewat body maka menggunakan 'form_paranms'
            'form_params' => $data
        ]);  
        // 
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }

    public function hapusDataMahasiswa($id)
    {
        // hapus langsung ke database
        // $this->db->where('id', $id);
        // $this->db->delete('mahasiswa', ['id' => $id]);

        // hapus lewat request ke guzzle
        $response = $this->_client->request('DELETE', 'mahasiswa', [
            // parameter, lewat body maka menggunakan 'form_paranms'
            'form_params' => [
                'id' => $id,
                'wpu-key' => $this->apikey
            ]
        ]);

        // masuk ke dalam objext untuk mengambil datavalid nya, dan ubah jsonke array assoc(parameter kedua true)
        $result = json_decode($response->getBody()->getContents(), true);

        // kembalikan data
        return $result;

    }

    public function ubahDataMahasiswa()
    {
        // update langsung ke database
        // menggunakan validation dari CI
        $data = [
            "nama" => $this->input->post('nama', true),
            "nrp" => $this->input->post('nrp', true),
            "email" => $this->input->post('email', true),
            "jurusan" => $this->input->post('jurusan', true),
            "id" => $this->input->post('id', true),
            "wpu-key" => $this->apikey
        ];

        // $this->db->where('id', $this->input->post('id'));
        // $this->db->update('mahasiswa', $data);

        // update data ke rest-server lewat guzzle
        $response = $this->_client->request('PUT', 'mahasiswa', [
            // parameter, lewat body maka menggunakan 'form_paranms'
            'form_params' => $data
        ]);

        // 
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;

    }

    public function cariDataMahasiswa()
    {
        // simpan keyword yang dimasukan user
        $keyword = $this->input->post('keyword', true);
        // eksekusi / penyocokan data
        $this->db->like('nama', $keyword);
        $this->db->or_like('jurusan', $keyword);
        $this->db->or_like('nrp', $keyword);
        $this->db->or_like('email', $keyword);
        // kembalian nilai / hasil
        return $this->db->get('mahasiswa')->result_array();
    }
}
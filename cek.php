<?php
    public function index()
    {
        $this->form_validation->set_rules('nama_pengrajin', 'Nama Pengrajin', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('kecamatan_id', 'Kecamatan', 'required');
        $this->form_validation->set_rules('desa_id', 'Desa', 'required');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required');
        $this->form_validation->set_rules('nomor_telepon', 'Nomor Telepon', 'required');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required');

        if ($this->form_validation->run() == TRUE) {
            $upload = $_FILES['foto']['name'];
            if ($upload) { // JIKA UPLOAD
                $jumlahFoto = sizeof($upload);
                $files = $_FILES['foto'];
                $config['upload_path']          = './assets/gambar/';
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 2000;
                $this->load->library('upload', $config);
                // Perulangan untuk get data gambar yang diupload
                for ($i = 0; $i < $jumlahFoto; $i++) {
                    $_FILES['foto']['name'] = $files['name'][$i];
                    $_FILES['foto']['type'] = $files['type'][$i];
                    $_FILES['foto']['tmp_name'] = $files['tmp_name'][$i];
                    $_FILES['foto']['error'] = $files['error'][$i];
                    $_FILES['foto']['size'] = $files['size'][$i];

                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('foto')) {
                        $data = $this->upload->data();
                        $namaGambar = $data['file_name'];
                        $insert[$i]['nama_gambar'] = $namaGambar;
                    }
                }
                if ($this->upload->display_errors()) { // Jika Data Kosong
                    $data = array(
                        'title'         => 'Input Data Masyarakat',
                        'sidebar'       => 'Masyarakat',
                        'error_upload'  => $this->upload->display_errors(),
                        'kecamatan'   => $this->M_kecamatan->tampil(),
                    );
                    $this->load->view('Users/Register', $data, FALSE);
                } else {
                    $dataKerajinan = array(
                        'nama_pengrajin'  => $this->input->post('nama_pengrajin'),
                        'produk'          => $this->input->post('produk'),
                        'alamat'          => $this->input->post('alamat'),
                        'kecamatan_id'    => $this->input->post('kecamatan_id'),
                        'desa_id'         => $this->input->post('desa_id'),
                        'latitude'        => $this->input->post('latitude'),
                        'longitude'       => $this->input->post('longitude'),
                        'deskripsi'       => $this->input->post('deskripsi'),
                        'nomor_telepon'   => $this->input->post('nomor_telepon'),
                        'status'          => "Tunggu",
                        'sumber'          => "masyarakat",
                        'ketersediaan'    => "aktif"
                    );
                    $this->M_Kerajinan->simpan($dataKerajinan);
                    // Get Last ID
                    $lastId = $this->M_gambar->getLastId();

                    for ($i = 0; $i < $jumlahFoto; $i++) {
                        $insert[$i]['kerajinan_id'] = $lastId;
                    }
                    if ($this->M_gambar->upload($insert, $data['file_name']) > 0) {
                        $this->session->set_flashdata('pesan', 'Data Gambar Berhasil Diupload');
                        redirect('Register');
                    } else {
                        $this->session->set_flashdata('pesan', 'Data Gambar Gagal Diupload');
                        redirect('Register');
                    }
                }
            }
        } else {
            // Jika admin belum melakukan inputan apapun masuk kesini
            $data = array(
                'title'       => 'Data Masyarakat',
                'sidebar'     => 'Masyarakat',
                'kecamatan'   => $this->M_kecamatan->tampil(),
                'isi'         => 'Users/Register'
            );
            $this->load->view('Users/Register', $data, FALSE);
        }
    }

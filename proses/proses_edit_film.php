<?php
include "connect.php";

$nama_film = (isset($_POST['nama_film'])) ? htmlentities($_POST['nama_film']) : "";
$jenis_film = (isset($_POST['jenis_film'])) ? htmlentities($_POST['jenis_film']) : "";
$produser = (isset($_POST['produser'])) ? htmlentities($_POST['produser']) : "";
$sutradara = (isset($_POST['sutradara'])) ? htmlentities($_POST['sutradara']) : "";
$penulis = (isset($_POST['penulis'])) ? htmlentities($_POST['penulis']) : "";
$produksi = (isset($_POST['produksi'])) ? htmlentities($_POST['produksi']) : "";
$durasi = (isset($_POST['durasi'])) ? htmlentities($_POST['durasi']) : "";
$trailer = (isset($_POST['trailer'])) ? htmlentities($_POST['trailer']) : "";
$casts = (isset($_POST['casts'])) ? htmlentities($_POST['casts']) : "";
$sinopsis = (isset($_POST['sinopsis'])) ? htmlentities($_POST['sinopsis']) : "";

$kode_rand = rand(10000, 99999)."-";
$target_dir = "../assets/img/film/".$kode_rand;
$target_file = $target_dir . basename($_FILES['foto']['name']);
$imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

$foto_lama = (isset($_POST['foto_lama'])) ? htmlentities($_POST['foto_lama']) : "";
$id_film = (isset($_POST['id_film'])) ? $_POST['id_film'] : "";

$statusUpload = 1;
if (!empty($_POST['input_film_validate'])) {
    // Cek apakah gambar diunggah atau tidak
    if (!empty($_FILES['foto']['tmp_name'])) {
        // Proses gambar baru
        $cek = getimagesize($_FILES['foto']['tmp_name']);
        if ($cek == false) {
            $message = "Ini Bukan file gambar";
            $statusUpload = 0;
        } else {
            $statusUpload = 1;
            if(file_exists($target_file)) {
                $message = "Maaf, File yang Dimasukkan Telah ada";
                $statusUpload = 0;
            } else {
                if($_FILES['foto']['size'] > 500000) { //500kb
                    $message = "File foto yang Dimasukkan terlalu besar";
                    $statusUpload = 0;
                } else {
                    if($imageType != "jpg" && $imageType != "png" && $imageType != "jpeg" && $imageType != "gif") {
                        $message = "Maaf, hanya diperbolehkan gambar yang memiliki format JPG, JPEG, PNG dan GIF";
                        $statusUpload = 0;
                    } else {
                        // Buat direktori jika tidak ada
                        if (!is_dir($target_dir)) {
                            mkdir($target_dir, 0777, true);
                        }

                        // Pindahkan file baru
                        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                            // Hapus foto lama jika ada
                            if (!empty($foto_lama)) {
                                unlink("../assets/img/film/" . $foto_lama);
                            }
                        }
                    }
                }
            }
        }
    } else {
        // Gunakan foto lama
        $kode_rand = substr($foto_lama, 0, 6); // Ambil kode_rand dari foto lama
        $target_file = "../assets/img/film/" . $kode_rand . $foto_lama;
    }

    if($statusUpload == 0) {
        $message = '<script>alert("'.$message.', Gambar tidak dapat diupload");
                    window.location="../Film"</script>';
    } else {
        $select = mysqli_query($conn, "SELECT * FROM tb_film WHERE nama_film = '$nama_film' AND id_film != '$id'");
        if (mysqli_num_rows($select) > 0) {
            $message = '<script>alert("Nama Film yang dimasukkan telah ada");
                        window.location="../Film"</script>';
        } else {
            $query = mysqli_query($conn, "UPDATE tb_film SET foto ='".$foto_lama."', nama_film='$nama_film', jenis_film='$jenis_film', produser='$produser', sutradara='$sutradara', penulis='$penulis', produksi='$produksi',casts='$casts',durasi='$durasi',trailer='$trailer',sinopsis='$sinopsis' WHERE id_film = '$id_film'");
            if ($query) {
                $message = '<script>alert("Data berhasil dimasukkan");
                    window.location="../Film"</script>';
            } else {
                $message = '<script>alert("Data gagal dimasukkan");
                    window.location="../Film"</script>';
            }
        }
    }
}

echo $message;
?>

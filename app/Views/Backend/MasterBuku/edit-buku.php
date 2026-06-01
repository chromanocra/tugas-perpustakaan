<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Buku</li>
            <li class="active">Input Data Buku</li>
        </ol>
    </div>
    <!--/.row-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Input Buku</h3>
                    <hr />
                    <form action="<?= base_url('admin/update-buku');?>" method="post" enctype="multipart/form-data">
                        <div class="form-group col-md-6">
                            <label>Judul Buku</label>
                            <input type="text" class="form-control" value="<?= $data_buku['judul_buku'];?>"
                                name="judul_buku" placeholder="Masukkan Judul Buku" required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Pengarang</label>
                            <input type="text" class="form-control" name="pengarang"
                                value="<?= $data_buku['pengarang'];?>" placeholder="Masukkan Nama Pengarang"
                                required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Penerbit</label>
                            <input type="text" class="form-control" name="penerbit"
                                value="<?= $data_buku['penerbit'];?>" placeholder="Masukkan Nama Penerbit"
                                required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Tahun</label>
                            <input type="text" class="form-control" name="tahun" placeholder="Masukkan Tahun"
                                value="<?= $data_buku['tahun'];?>" required="required"
                                onkeypress="return goodchars(event,'0123456789',this)">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Jumlah Eksemplar</label>
                            <input type="number" class="form-control" name="jumlah_eksemplar"
                                value="<?= $data_buku['jumlah_eksemplar'];?>" placeholder="Masukkan Jumlah Eksemplar"
                                required="required">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Kategori Buku</label>
                            <select class="form-control" name="id_kategori" value="" required="required">
                                <option value="">-- Pilih Kategori Buku --</option>
                                <?php foreach($data_kategori as $kat){ ?>
                                <option value="<?= $kat['id_kategori'];?>"
                                    <?= ($kat['id_kategori'] == $data_buku['id_kategori']) ? 'selected' : ''; ?>>
                                    <?= $kat['nama_kategori'];?>
                                </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Keterangan</label>
                            <input type="text" class="form-control" value="<?= $data_buku['keterangan'];?>"
                                name="keterangan" placeholder="Masukkan Keterangan">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Rak</label>
                            <select class="form-control" name="id_rak" required="required">
                                <option value="">-- Pilih Rak --</option>

                                <?php foreach($data_rak as $rak){ ?>

                                <option value="<?= $rak['id_rak'];?>"
                                    <?= ($rak['id_rak'] == $data_buku['id_rak']) ? 'selected' : ''; ?>>

                                    <?= $rak['nama_rak'];?>

                                </option>

                                <?php } ?>
                            </select>
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>Cover Buku</label>

                            <input type="file" class="form-control" name="cover_buku">

                            <small>
                                Format file: jpg, jpeg, png. Maks 1 MB
                            </small>

                            <br><br>

                            <img src="<?= base_url('Assets/CoverBuku/'.$data_buku['cover_buku']);?>" width="120">
                            <input type="hidden" name="old_cover" value="<?= $data_buku['cover_buku'];?>">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <label>E-Book</label>

                            <input type="file" class="form-control" name="e_book">

                            <small>
                                Format file pdf. Maks 10 MB
                            </small>

                            <br><br>

                            <a target="_blank" href="<?= base_url('Assets/E-Book/'.$data_buku['e_book']);?>">
                                Lihat E-Book Lama
                            </a>
                            <input type="hidden" name="old_ebook" value="<?= $data_buku['e_book'];?>">
                        </div>
                        <div style="clear:both;"></div>

                        <div class="form-group col-md-6">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="<?= base_url('admin/master-data-buku-buku');?>">
                                <button type="button" class="btn btn-danger">Batal</button>
                            </a>
                        </div>
                        <div style="clear:both;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
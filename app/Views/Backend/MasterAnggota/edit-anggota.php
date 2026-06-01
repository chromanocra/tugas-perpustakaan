<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li>Master Data Anggota</li>
            <li class="active">Edit Data Anggota</li>
        </ol>
    </div>
    <!--/.row-->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Edit Anggota</h3>
                    <hr />
                    <form action="<?php echo base_url('admin/update-anggota'); ?>" method="post">
                        <div class="form-group col-md-6">
                            <label>Nama Anggota</label>
                            <input type="text" class="form-control" name="nama"
                                value="<?= $data_anggota['nama_anggota']; ?>" required="required">
                        </div>
                        <div style="clear: both;"></div>
                        <div class="form-group col-md-6">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required="required">
                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L" <?= $data_anggota['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
                                    Laki-laki
                                </option>
                                <option value="P" <?= $data_anggota['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
                                    Perempuan
                                </option>
                            </select>
                        </div>
                        <div style="clear: both;"></div>
                        <div class="form-group col-md-6">
                            <label>No. TLP</label>
                            <input type="text" class="form-control" name="no_telp"
                                value="<?= $data_anggota['no_telp']; ?>" required="required"
                                onkeypress="return goodchars(event,'0123456789',this)">
                        </div>
                        <div style="clear: both;"></div>
                        <div class="form-group col-md-6">
                            <label>Alamat</label>
                            <input type="text" class="form-control" name="alamat"
                                value="<?= $data_anggota['alamat']; ?>" required="required">
                        </div>
                        <div style="clear: both;"></div>
                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" value="<?= $data_anggota['email']; ?>"
                                required="required">
                        </div>
                        <div style="clear: both;"></div>
                        <div class="form-group col-md-6">
                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="<?= base_url('admin/master-data-anggota'); ?>">
                                <button type="button" class="btn btn-danger">Batal</button>
                            </a>
                        </div>
                        <div style="clear: both;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
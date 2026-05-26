<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
    <div class="row">
        <ol class="breadcrumb">
            <li><a href="#"><span class="glyphicon glyphicon-home"></span></a></li>
            <li class="active">Master Data Buku</li>
        </ol>
    </div><!--/.row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Data Buku
                        <a href="<?= base_url('admin/input-buku');?>">
                            <button type="button" class="btn btn-sm btn-primary pull-right">+ Tambah Data Buku</button>
                        </a>
                    </h3>
                    <hr />
                    <table data-toggle="table" data-show-refresh="true" data-show-toggle="true"
                    data-show-columns="true" data-search="true" data-select-item-name="toolbar1"
                    data-pagination="true" data-sort-name="name" data-sort-order="desc">
                        <thead>
                            <tr>
                                <th data-sortable="true">No</th>
                                <th data-sortable="true">Cover Buku</th>
                                <th data-sortable="true">Judul Buku</th>
                                <th data-sortable="true">Pengarang</th>
                                <th data-sortable="true">Penerbit</th>
                                <th data-sortable="true">Tahun</th>
                                <th data-sortable="true">Jumlah Eksemplar</th>
                                <th data-sortable="true">Kategori Buku</th>
                                <th data-sortable="true">Keterangan</th>
                                <th data-sortable="true">Rak</th>
                                <th data-sortable="true">E-Book</th>
                                <th data-sortable="true">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 0;
                            foreach($data_buku as $data){
                            ?>
                            <tr>
                                <td><?= $no=$no+1;?></td>
                                <td><img src="/Assets/CoverBuku/<?= $data['cover_buku'];?>" width="60px"></td>
                                <td><?= $data['judul_buku'];?></td>
                                <td><?= $data['pengarang'];?></td>
                                <td><?= $data['penerbit'];?></td>
                                <td><?= $data['tahun'];?></td>
                                <td><?= $data['jumlah_eksemplar'];?></td>
                                <td><?= $data['nama_kategori'];?></td>
                                <td><?= $data['keterangan'];?></td>
                                <td><?= $data['nama_rak'];?></td>
                                <td>
                                    <a href="/Assets/E-Book/<?= $data['e_book'];?>" target="_blank">
                                        <?= $data['e_book'];?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/edit-buku/'.sha1($data['id_buku']));?>">
                                        <button type="button" class="btn btn-sm btn-success">Edit</button>
                                    </a>
                                    <a href="#" onclick="doDelete('<?= sha1($data['id_buku']);?>')">
                                        <button type="button" class="btn btn-sm btn-danger">Hapus</button>
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
function doDelete(idDelete) {
    if(confirm("Yakin ingin menghapus data buku ini?")) {
        window.location.href = '<?= base_url() ?>/admin/hapus-buku/' + idDelete;
    }
}
</script>
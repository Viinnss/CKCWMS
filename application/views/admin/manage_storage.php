<section>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body pt-3">
					<table class="table" id="tbl-report-usage">
						<thead>
							<tr>
								<th class="text-center">#</th>
								<th class="text-left">Material No</th>
								<th class="text-left">Material Name</th>
								<th class="text-center">Qty</th>
								<th class="text-center">Uom</th>
                                <th class="text-left">Transaction Type</th>
								<th class="text-left">Update At</th>
								<th class="text-left">Action</th>
							</tr>
						</thead>
						<tbody class="tbody-report-usage">
							<!-- Table rows will be appended here by JavaScript -->
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<!-- SPINNER LOADING -->
<div class="spinner-container" id="spinner-container">
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<div class="spinner-grow text-success" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
</div>

<script src="<?= base_url('assets'); ?>/js/functions.js"></script>
<script>
	$(document).ready(function() {
		$('#spinner-container').show();

		$.ajax({
			url: '<?= base_url('admin/load_manage_storage'); ?>',
			type: 'get',
			dataType: 'json',
			data: {},
			success: function(res) {
				$('#spinner-container').hide();

				var $tbody = $('.tbody-report-usage');

				// Loop over the data and build table rows
				$.each(res, function(index, material) {
					var row = `<tr>
						<td class="text-center">${(index + 1)} </td>
						<td class="text-start">${material.Material_no} </td>
						<td class="text-left">${material.Material_name} </td> 
						<td class="text-center">${formatQuantity(material.Qty, material.Unit)}</td> 
						<td class="text-center">${material.Unit}</td>
                        <td class="text-center">${material.Transaction_type} </td> 
						<td class="text-center">${material.Updated_at}</td> 
						<td  class="text-center align-middle">
							<button class="btn btn-danger ms-1 btn-delete-storage" 
								data-id="${material.Id}">
								<i class="bx bxs-trash"></i>
							</button>
						</td>
						</tr>`;
					$tbody.append(row);
				});

				// Transform the table into a DataTable
				$('#tbl-report-usage').DataTable({
					columnDefs: [{
							targets: 1,
							className: 'text-start'
						} // Force left alignment on column 1
					],
					layout: {
						topStart: {
							buttons: [{
								extend: 'excel',
								text: '<i class="bx bx-table"></i> Excel',
								className: 'btn-custom-excel'
							}]
						}
					}
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				console.error(xhr.statusText);
			}
		});

		$(document).on('click', '.btn-delete-storage', function() {
			const id = $(this).data('id');
			Swal.fire({
				title: 'Apakah Anda Yakin?',
				text: "Anda tidak bisa mengembalikan ini!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Ya, hapus saja!',
				cancelButtonText: 'Jangan!'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = '<?= base_url("admin/delete_storage_item/"); ?>' + id;
				}
			});
		});
	});
</script>

<!-- SweetAlert Notifications -->
<?php if ($this->session->flashdata('SUCCESS_DELETE')): ?>
<script>
    Swal.fire({
        title: "Deleted!",
        text: "<?= $this->session->flashdata('SUCCESS_DELETE'); ?>",
        icon: "success"
    });
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_DELETE')): ?>
<script>
    Swal.fire({
        title: "Failed!",
        text: "<?= $this->session->flashdata('FAILED_DELETE'); ?>",
        icon: "error"
    });
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('ERROR')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Error",
				html: `<?= $this->session->flashdata('ERROR'); ?>`,
				icon: "error"
			});
		});
	</script>
<?php endif; ?>

<!-- <?php if ($this->session->flashdata('SUCCESS')): ?>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			Swal.fire({
				title: "Success",
				html: `<?= $this->session->flashdata('SUCCESS'); ?>`,
				icon: "success"
			});
		});
	</script>
<?php endif; ?> -->

<!-- <?php if ($this->session->flashdata('SUCCESS_editMaterial')): ?>
<script>
    Swal.fire({
        title: "Success!",
        text: "<?= $this->session->flashdata('SUCCESS_editMaterial'); ?>",
        icon: "success"
    });
</script>
<?php endif; ?>

<?php if ($this->session->flashdata('FAILED_editMaterial')): ?>
<script>
    Swal.fire({
        title: "Error!",
        text: "<?= $this->session->flashdata('FAILED_editMaterial'); ?>",
        icon: "error"
    });
</script>
<?php endif; ?> -->

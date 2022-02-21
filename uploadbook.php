<?php
	session_start();
	include('checklogin.php');
	include('assets/libary/constant.php');

	$user_id = $_SESSION['user_id'];


	if(array_key_exists('addBook', $_POST)){
		$bookname = $_POST['book_name'];
		$author = $_POST['author'];
		$description = $_POST['description'];

		$coverimg = $_FILES['cover_img'];
		$book = $_FILES['book'];

		
		// print_r($book); exit();

		// ///processing img

		



		if(empty($bookname) || empty($author) || empty($description)) {
			$report = 'All fileds are required'; $count = 1;
		}else {
			if(empty($book['name']) || empty($coverimg['name']) ) {
				$report = 'The cover image and book pdf is required'; $count = 1;
			}else {
				$coverimg_extension = explode('.', $coverimg['name'])[1];
				$img_array = ['jpeg', 'jpg', 'gif', 'png'];
				$check_cover_extension = in_array($coverimg_extension, $img_array);
				$coverimg_name = $bookname.'_'.$author.'.'.$coverimg_extension;


				$book_extension = explode('.', $book['name'])[1];
				$book_array = ['pdf', 'docs', 'txt'];
				$check_book_extension = in_array($book_extension, $book_array);
				$book_link = $bookname.'_'.$author.'.'.$book_extension;

				if($check_book_extension == true || $check_cover_extension == true) {
					move_uploaded_file($coverimg['tmp_name'], 'assets/img/'.$coverimg_name);
					move_uploaded_file($book['tmp_name'], 'assets/docs/'.$book_link);

					$sql = $db->query("INSERT INTO books(name,description, author, cover_img, doc_link, user_id) VALUES('$bookname', '$description', '$author', '$coverimg_name', '$book_link', '$user_id')  ")or die(mysqli_error($db));

					$report = 'Book added sucessfully'; $count = 0;
				}else {
					$report = 'The image or document is not valid'; $count = 1;
				}




			}
		}




	}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Bookep | Dashboard</title>
	<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/dashboard.css">
	<script type="text/javascript" href="assets/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
	<?php include('nav.php'); ?>

	<?php if(isset($report)){ echo alert($report, $count); } ?>

		<div class="container mt-3">
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-header">
							<h3 class="card-title m-0">Add A Book</h3>
						</div>
						<div class="card-body">
							<form class="row" method="post" enctype="multipart/form-data">
								<div class="col-md-8 form-group">
									<label>Book Title</label>
									<input type="text" name="book_name" class="form-control">
								</div>


								<div class="col-md-4 form-group">
									<label>Author</label>
									<input type="text" name="author" class="form-control">
								</div>


								<div class="col-md-6 mt-2 form-group">
									<label>Book Cover Image</label>
									<input type="file" name="cover_img" class="form-control" accept="image/*">
								</div>


								<div class="col-md-6 mt-2 form-group">
									<label>PDF Malterial</label>
									<input type="file" name="book" class="form-control">
								</div>


								<div class="col-md-12 mt-2 form-group">
									<label>Book Description</label>
									<textarea name="description" rows="4" class="form-control"></textarea>
								</div>

								<div class="col-12 form-group">
									<button class="btn btn-primary mt-2" name="addBook" style="float:right">Save Book Info</button>
								</div>

							</form>
						</div>
					</div>

					<div class="card mt-3">
						<div class="card-header">
							<h3 class="card-title m-0">Uploaded Books</h3>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>#</th>
											<th>Title</th>
											<th>Author</th>
											<th>Description</th>
											<th>Date Added</th>
											<th></th>
										</tr>
									</thead>
									<tbody>

										<?php  
											$i = 1;
											$sql = $db->query("SELECT * FROM books WHERE user_id=$user_id ");
											while ($row = mysqli_fetch_array($sql)) {
										?>
											<tr>
												<td><?= $i++ ?></td>
												<td><?= $row['name'] ?></td>
												<td><?= $row['author'] ?></td>
												<td><?= $row['description'] ?></td>
												<td><?=  date('j M, Y', strtotime($row['created_at'])) ?></td>
												<td>
													<div class="d-flex justify-content-between">
														<button class="btn btn-info btn-sm">Edit</button>
														<button class="btn btn-danger btn-sm">Del</button>
														<button class="btn btn-secondary btn-sm">Vie</button>
													</div>
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
		</div>

	<?php include('footer.php')  ?>
</body>
</html>
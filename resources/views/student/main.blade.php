@ extends('layout.theme.main')
@section('cintent')

body class="bg-light">

  
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.php">Student Management</a>
      <form method="POST" action="">
        <button type="submit" class="btn btn-danger" name="logout">Logout</button>
      </form>
    </div>
  </nav>

  <div class="container">
    <div class="row">

     
      <div class="col-md-12">
        <div class="card shadow-sm mb-4">
          <div class="card-header bg-secondary text-white">Student List</div>
          <div class="card-body">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Profile</th>
                  <th>Name</th>
                  <th>Age</th>
                  <th>Course</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                  <tr>
                    <td><?= $row['id']; ?></td>

                    
                    <td>
                      <?php if (!empty($row['profile']) && file_exists($row['profile'])): ?>
                        <img src="<?= $row['profile']; ?>" class="profile-img" alt="Profile">
                      <?php else: ?>
                        <img src="uploads/default.png" class="profile-img" alt="Default Profile">
                      <?php endif; ?>
                    </td>

                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['age']); ?></td>
                    <td><?= htmlspecialchars($row['course']); ?></td>
                    <td>
                      <?php if ($row['role'] === 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                      <?php elseif ($row['role'] === 'faculty'): ?>
                        <span class="badge bg-success">Faculty</span>
                      <?php else: ?>
                        <span class="badge bg-primary">Student</span>
                      <?php endif; ?>
                    </td>
                    <td>
                     
                      <a href="edit_student.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Update</a>

                      
                      <form method="POST" action="delete.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                        <button type="submit" name="action" value="delete" class="btn btn-danger btn-sm">Delete</button>
                      </form>
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

</body>
</html>
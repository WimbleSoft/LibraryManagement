<?php
require __DIR__ . '/includes/bootstrap.php';

$currentUser = require_admin($connection);

if (is_post()) {
    verify_csrf();

    $action = $_POST['action'] ?? '';
    $bookId = post_int('bookId');
    $bookName = post_string('bookName', 100);
    $bookAuthor = post_string('bookAuthor', 100);

    if ($action === 'add' || $action === 'update') {
        if ($bookName === '' || $bookAuthor === '') {
            flash('danger', t('Fill_All_Fields'));
        } elseif ($action === 'add') {
            $connection
                ->prepare('INSERT INTO books (bookName, bookAuthor) VALUES (?, ?)')
                ->execute([$bookName, $bookAuthor]);
            flash('success', t('Process_Successful'));
        } elseif ($bookId !== null) {
            $connection
                ->prepare('UPDATE books SET bookName = ?, bookAuthor = ? WHERE bookId = ?')
                ->execute([$bookName, $bookAuthor, $bookId]);
            flash('success', t('Process_Successful'));
        }
    } elseif ($action === 'delete' && $bookId !== null) {
        delete_item($connection, 'books', 'bookId', ITEM_BOOK, $bookId);
    }

    redirect('books.php');
}

$books = $connection->query('SELECT bookId, bookName, bookAuthor FROM books ORDER BY bookName')->fetchAll();
$openLoans = open_loans_by_item($connection, ITEM_BOOK);

$activePage = 'books';
$pageTitle = t('Books');
require __DIR__ . '/includes/header.php';
?>
<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title"><?= e(t('Add_New_Book')) ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="post" action="books.php" class="row">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="add">
            <div class="col-md-5 form-group">
                <input name="bookName" type="text" class="form-control" maxlength="100" required placeholder="<?= e(t('Enter_Book_Name')) ?>">
            </div>
            <div class="col-md-5 form-group">
                <input name="bookAuthor" type="text" class="form-control" maxlength="100" required placeholder="<?= e(t('Enter_Book_Author')) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><?= e(t('Add_Book')) ?></button>
            </div>
        </form>
    </div>
</div>

<div class="box">
    <div class="box-body table-responsive">
        <table class="table table-bordered table-striped data-table">
            <thead>
            <tr>
                <th><?= e(t('Book_Name')) ?></th>
                <th><?= e(t('Author')) ?></th>
                <th><?= e(t('Status')) ?></th>
                <th><?= e(t('Operations')) ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($books as $book): ?>
                <?php $loan = $openLoans[(int) $book['bookId']] ?? null; ?>
                <tr>
                    <td><?= e($book['bookName']) ?></td>
                    <td><?= e($book['bookAuthor']) ?></td>
                    <td><?php require __DIR__ . '/includes/loan-status.php'; ?></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editBook<?= e($book['bookId']) ?>" title="<?= e(t('Edit')) ?>">
                            <i class="fa fa-pencil"></i>
                        </button>
                        <form method="post" action="books.php" class="inline-form" data-confirm="<?= e(t('Confirm_Delete')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="bookId" value="<?= e($book['bookId']) ?>">
                            <button type="submit" class="btn btn-danger btn-sm" title="<?= e(t('Delete')) ?>"><i class="fa fa-trash"></i></button>
                        </form>

                        <div class="modal fade" id="editBook<?= e($book['bookId']) ?>" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <form method="post" action="books.php" class="modal-content">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="bookId" value="<?= e($book['bookId']) ?>">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= e(t('Close')) ?>">&times;</button>
                                        <h4 class="modal-title"><?= e(t('Edit_Book')) ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label><?= e(t('Book_Name')) ?></label>
                                            <input name="bookName" type="text" class="form-control" maxlength="100" required value="<?= e($book['bookName']) ?>">
                                        </div>
                                        <div class="form-group">
                                            <label><?= e(t('Author')) ?></label>
                                            <input name="bookAuthor" type="text" class="form-control" maxlength="100" required value="<?= e($book['bookAuthor']) ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?= e(t('Cancel')) ?></button>
                                        <button type="submit" class="btn btn-warning"><?= e(t('Save')) ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

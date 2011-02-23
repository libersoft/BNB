<?php use_helper("Form") ?>

<h2>Requested page needs the user to be authenticated.</h2>

<?php if (isset($errors) && $errors == true): ?>
<p class="errors_found_box">
    Username or password are not valid.
</p>
<?php endif ?>

<?php echo form_tag("general/login") ?>
<table class="table-form container" border="0" cellpadding="0" cellspacing="0" style="text-align: left">
    <?php echo $form ?>
    <tr>
        <td />
        <td align="left">
            <input type="submit" value="Login" />
        </td>
    </tr>
</table>
</form>

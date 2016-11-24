<?

if (secure_privilege("srv_warn"))
{
  $shell = shell_exec("sudo /usr/lib/iserv/stsbl_repo_print_umode");
  $mode = trim($shell);
  if ($mode == "testing" or $mode == "unstable")
  {
    // Heading
    echo "<tr>";
    echo "<td colspan='2'>";
    printf("<h2>".icon("dlg-warn")._("%s updates (StsBl repository)")."</h2>", $mode);
    echo "</td>";
    echo "</tr>";

    // Content
    echo "<tr>";
    echo "<td colspan='2'>";
    printf(_("Your server is currently receiving %s updates from the repository of the Stadtteilschule Blankenese.").'<br />', $mode);
    echo _("To change that, login as root and run the command stsbl-repoconfig.");
    echo "</td>";
    echo "</tr>";
    echo "<tr><td>&nbsp;</td></tr>\n";
  }
}

?>

{if isset($user_name) && $user_name}
<span style="color:#333"><strong>Name:</strong> {$user_name}</span><br /><br />
{/if}
{if isset($email) && $email}
<span style="color:#333"><strong>E-mail: <a href="mailto:{$email}" style="color:#337ff1">{$email}</a></strong></span><br /><br />
{/if}
{if isset($subject) && $subject}
<span style="color:#333"><strong>Title:</strong> {$subject}</span><br /><br />
{/if}
{if isset($message) && $message}
<span style="color:#333"><strong>Customer message:</strong></span><br />{$message}<br/><br />
{/if}
{if isset($attached_file) && $attached_file}
<span style="color:#333"><strong>Attached file:</strong></span> {$attached_file}
{/if}

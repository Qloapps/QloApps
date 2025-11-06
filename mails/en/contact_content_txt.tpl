
{if isset($user_name) && $user_name}
	Name: {$user_name}
{/if}
{if isset($email) && $email}
	E-mail: {$email}
{/if}
{if isset($subject) && $subject}
	Title: {$subject}
{/if}

{if isset($message) && $message}
	Customer message:</strong></span><br />{$message}<br/><br />
{/if}
{if isset($attached_file) && $attached_file}
	Attached file:</strong></span> {$attached_file}
{/if}


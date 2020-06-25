# SecureForms

Create end-to-end encrypted surveys and forms. Check out the demo: forms.william-koch.com

## Disclaimer

This project is still under heavy development. Even if it uses end-to-end encryption, we cannot guarantee that the application is sufficiently secured against known hacks. We do not recomment using this tool for highly confidential items.

Due to the development nature of this web application it is possible that your account or account data including forms you have created and submission results are deleted.

## End-to-end encryption

Unlike other survey tools like Google Forms, Microsoft Forms, Limesurvey, Framaforms, Typeform and many others, **SecureForms** encrypts your form data so that only **you** and the people you share it with have access to it. Even if the server administrator wanted to, he or she could not possibly find out what people respond to your survey, or even what the questions in your survey are. This makes **SecureForms** the ideal tool not only for individuals, but also for companies and health industries working with confidential data. 

The general idea is to make your life as simple as possible. You should not have to wonder what end-to-end encryption is or how it works to use the application. SecureForms is as easy to use as any other survey tool, just much safer.

Here is an overview of what exactly is end-to-end encrypted. The form creator is called "you" in the following.

| Data                                     | Visible by                                   | Encryption type |
| ---------------------------------------- | -------------------------------------------- | --------------- |
| Form questions, including answer options | You, and people you share the form link with | Symmetric       |
| Form submissions                         | Only you                                     | Asymmetric      |

The title of your form is currently still visible to the server administrator. 



## FAQ

### When sharing a form, how can you (server admin) generate a link for me containing the key, without actually knowing the key? What prevents you from viewing the form?

The raw form data is encrypted with the key that is included in the link when you share the form. So without this key, the encrypted form data that the server admin has access to is completely useless (unreadable).

When you create a new form, you generate a key for it on the client side (in your browser, not on the server). This key is then encrypted with your public key (which was generated for you when you registered). This encrypted version is then stored for you on the server. Only with your private key can you optain the actual key again - however, the server admin does not have access to your private key. When you share the form, your browser unlocks the key to the form using your private key and then displays it to you - all of this happens in your browser, locally, and is never sent to the server!



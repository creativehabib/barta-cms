# GitHub-এ আপলোড · Push to GitHub

> এই ধাপগুলো **আপনার নিজের কম্পিউটারে** চালান। আপনার GitHub পাসওয়ার্ড, টোকেন বা কোনো credential আমাকে দেওয়ার দরকার নেই — সব কিছু আপনার হাতেই থাকে।
>
> Run these steps **on your own machine**. You never need to share your GitHub password, token, or any credential — everything stays with you.

---

## ১. GitHub-এ একটি খালি রিপোজিটরি তৈরি করুন · Create an empty repo

1. <https://github.com/new> এ যান
2. Repository name দিন, যেমন `barta-cms`
3. **README/ .gitignore / license যোগ করবেন না** (প্রজেক্টে ইতিমধ্যে আছে) — খালি রাখুন
4. **Create repository** ক্লিক করুন

---

## ২. লোকাল রিপো ইনিশিয়ালাইজ ও কমিট · Initialise & commit locally

আনজিপ করা `barta-cms` ফোল্ডারের ভিতরে টার্মিনাল খুলে চালান:

```bash
cd barta-cms

git init
git branch -M main
git add .
git commit -m "Initial commit: Barta news portal CMS (Laravel 13 + Livewire 3)"
```

> `.gitignore` ইতিমধ্যে `.env`, `vendor/`, `node_modules/` ইত্যাদি বাদ দেয় — কোনো secret বা ভারী ফোল্ডার কমিট হবে না।

---

## ৩. রিমোট যোগ করে পুশ · Add the remote & push

GitHub রিপো পেজে দেখানো URL ব্যবহার করুন। **HTTPS** (সহজ) অথবা **SSH** — যেকোনো একটি:

**HTTPS:**

```bash
git remote add origin https://github.com/<your-username>/barta-cms.git
git push -u origin main
```

**SSH:**

```bash
git remote add origin git@github.com:<your-username>/barta-cms.git
git push -u origin main
```

`<your-username>` কে আপনার আসল GitHub ইউজারনেম দিয়ে বদলান।

---

## ৪. অথেন্টিকেশন · Authentication

- **HTTPS**-এ push করার সময় GitHub ইউজারনেম ও একটি **Personal Access Token** (পাসওয়ার্ড নয়) চাইবে। টোকেন তৈরি: GitHub → *Settings → Developer settings → Personal access tokens*।
- **SSH** ব্যবহার করলে আগে একটি SSH key যোগ করে নিন: <https://docs.github.com/en/authentication/connecting-to-github-with-ssh>।

> 🔐 এই credential/টোকেন কেবল আপনার মেশিন ও GitHub-এর মধ্যে থাকে — কারও সাথে শেয়ার করবেন না, কোডে কমিট করবেন না।

---

## ৫. পরবর্তী আপডেট · Future updates

```bash
git add .
git commit -m "আপনার পরিবর্তনের বর্ণনা"
git push
```

---

## `.env` সম্পর্কে সতর্কতা · About `.env`

`.env` ফাইল **কখনো** কমিট হবে না (`.gitignore`-এ আছে)। এতে DB পাসওয়ার্ড, AI API key, পেমেন্ট secret থাকে — এগুলো গোপন রাখুন। নতুন সার্ভারে সেটআপের সময় `.env.example` কপি করে নতুন `.env` বানিয়ে মান বসান।

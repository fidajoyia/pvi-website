// ===== Institute ki basic information — yahan apni details likhein =====
const COLLEGE = {
  name: "Prime Vocational Institute",
  city: "Haroonabad",
  tagline: "Upgrade Your Skills, Build Your Future",
  subTagline: "Learn from experts and grow with confidence",
  affiliation: "Registered with NAVTTC (Govt. of Pakistan) & PSDA (Govt. of Punjab) · Affiliated with PBTE Lahore",
  admissionBadge: "Admission Open — Boys & Girls",
  established: "",                 // saal likhein, maslan "2015" (khali ho to nahi dikhta)
  address: "Main Road, Model Town, Haroonabad",
  phone: "0301-7681248",
  phone2: "0333-6314100",
  slogan: "Skill Today, Success Tomorrow",
  email: "",                       // email ho to likhein (khali ho to nahi dikhta)
  timing: "",                      // maslan "Mon - Sat, 8:00 AM - 4:00 PM" (khali ho to nahi dikhta)
  // Google Maps mein jo search karte hain wohi likhein (institute ka naam + shehar)
  mapQuery: "Model Town, Haroonabad",
  about:
    "Prime Vocational Institute, Haroonabad NAVTTC (Govt. of Pakistan) aur PSDA (Govt. of Punjab) se registered hai aur PBTE Lahore se affiliated hai. Hum IT, technical, skills aur medical/health ke shobon mein diploma aur certificate courses karwate hain, taake students hunar seekh kar rozgaar ya apna kaam shuru kar sakein.",
  principalMessage:
    "Hamare institute mein khush aamdeed. Hamara maqsad har student ko practical hunar dena hai jo use mulk aur bairon-e-mulk rozgaar ke qabil banaye.",
  stats: [
    { label: "NAVTTC", value: "Registered" },
    { label: "PSDA Punjab", value: "Registered" },
    { label: "PBTE Lahore", value: "Affiliated" },
    { label: "NAVTTC Institute", value: "Partner" },
  ],
  facilities: [
    "Computer Labs", "Electrical Workshop", "Plumbing Workshop", "Stitching / Fashion Lab",
    "Medical Training Lab", "Evening Classes", "Qualified Teachers", "Job Guidance",
  ],
};

// Course categories (Courses page par isi tarteeb se dikhti hain)
const CATEGORIES = [
  "Diploma Programs",
  "IT & Computer Courses",
  "Technical & Skill Courses",
  "Evening Coaching Classes (Medical / Health)",
  "NAVTTC Free Courses (PM Youth Programme)",
];

// ===== NAVTTC programme ki information =====
const NAVTTC_ELIGIBILITY = "Primary / Middle / Matric / Intermediate · Umar 18-45 saal · Male & Female";
const NAVTTC_INFO = {
  applyUrl: "https://nsis.navttc.gov.pk",
  documents: "Shanakhti card (CNIC), 4 passport size tasaveer, taleemi asnaad aur result card ki copy",
};

// ===== Posters (images folder mein) =====
const POSTERS = [
  { src: "images/admission-open.jpg", title: "Admission Open — Boys & Girls" },
  { src: "images/dit-admission.jpg", title: "DIT — Diploma in Information Technology" },
  { src: "images/navttc-free-courses.jpg", title: "NAVTTC & PM Youth Programme — Free Courses" },
  { src: "images/navttc-partner.jpg", title: "NAVTTC Partner Institute" },
];

// ===== Default data (Admin page se aur add kar sakte hain) =====
// Jo field maloom na ho (fee, seats waghera) usay khali chhor dein — page par nahi dikhegi.
const DEFAULT_COURSES = [
  // Diploma Programs
  {
    code: "DIT", category: "Diploma Programs", name: "DIT (Diploma of Information Technology)",
    duration: "2 Saal (Semester System)", note: "ICS ke barabar · Approved by PBTE", eligibility: "Matric pass",
    features: [
      "2 Year Semester System Program",
      "Practical approach ke saath",
      "Pehle saal 9 computer courses",
      "Doosre saal English, Urdu, Math waghera",
      "Pehle saal students ko project diya jata hai",
    ],
  },
  { code: "MTECH", category: "Diploma Programs", name: "Matric Tech", duration: "2 Saal" },
  { code: "CO", category: "Diploma Programs", name: "Computer Operator", duration: "1 Saal" },

  // IT & Computer Courses
  { code: "CCA", category: "IT & Computer Courses", name: "Certificate in Computer Application (CCA)", duration: "6 Mahine" },
  { code: "DMSEO", category: "IT & Computer Courses", name: "Digital Marketing & SEO" },
  { code: "ECOM", category: "IT & Computer Courses", name: "E-Commerce" },
  { code: "AIECOM", category: "IT & Computer Courses", name: "AI Powered E-Commerce" },
  { code: "ANDROID", category: "IT & Computer Courses", name: "Android Mobile Phone Application" },

  // Technical & Skill Courses
  { code: "FASHION", category: "Technical & Skill Courses", name: "Fashion Designing & Dress Making" },
  { code: "ELEC", category: "Technical & Skill Courses", name: "Electrician" },
  { code: "PLUMB", category: "Technical & Skill Courses", name: "Plumber & Pipe Fitter" },
  { code: "SAFETY", category: "Technical & Skill Courses", name: "Safety Inspector" },
  { code: "SURVEY", category: "Technical & Skill Courses", name: "Civil Surveyor" },

  // Evening Coaching Classes
  { code: "LHV", category: "Evening Coaching Classes (Medical / Health)", name: "LHV (Lady Health Visitor)", duration: "2 Saal" },
  { code: "CNA", category: "Evening Coaching Classes (Medical / Health)", name: "CNA (Certified Nursing Assistant)", duration: "2 Saal" },
  { code: "CMW", category: "Evening Coaching Classes (Medical / Health)", name: "CMW (Community Midwife)" },
  { code: "PHARM", category: "Evening Coaching Classes (Medical / Health)", name: "Pharmacy Technician", duration: "2 Saal" },
  { code: "LAB", category: "Evening Coaching Classes (Medical / Health)", name: "Lab Technician" },
  { code: "OTT", category: "Evening Coaching Classes (Medical / Health)", name: "OTT (Operation Theatre Technician)" },
  { code: "DISP", category: "Evening Coaching Classes (Medical / Health)", name: "Dispenser Technician" },
  { code: "DENTAL", category: "Evening Coaching Classes (Medical / Health)", name: "Dental Technician" },
  { code: "RADIO", category: "Evening Coaching Classes (Medical / Health)", name: "Radiographer" },

  // NAVTTC Free Courses (PM Youth Programme)
  { code: "N-SURVEY", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Advance Survey (Civil)", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-FASHION", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Fashion Design & Dress Making", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-ELEC", category: "NAVTTC Free Courses (PM Youth Programme)", name: "General Electrician", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-ECOM", category: "NAVTTC Free Courses (PM Youth Programme)", name: "eCommerce", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-MASON", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Mason", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-JS", category: "NAVTTC Free Courses (PM Youth Programme)", name: "JavaScript Fullstack (MEAN / MERN)", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-WELD", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Welder (SMAW, GTAW)", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-WEB", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Advance Web Application Development", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-PLUMB", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Plumber / Pipe Fitter", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-AVA", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Amazon Virtual Assistant", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-CNC", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Advanced CNC", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-AI", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Artificial Intelligence (Machine Learning)", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-ARC", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Electric Arc Welding", duration: "6 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
  { code: "N-DMSEO", category: "NAVTTC Free Courses (PM Youth Programme)", name: "Digital Marketing & Search Engine Optimization", duration: "3 Mahine", fee: "Muft (Free)", eligibility: NAVTTC_ELIGIBILITY },
];

const DEFAULT_NOTIFICATIONS = [
  { id: 1, date: "2026-10-01", title: "Admissions Open — Boys & Girls", text: "DIT, Matric Tech, Computer Operator, CCA, Digital Marketing, E-Commerce aur tamam skill courses mein admissions jari hain. Online apply karein ya call karein: 0301-7681248.", important: true },
  { id: 3, date: "2026-05-01", title: "Classes Starting 15 May 2026", text: "DIT (2 Year), Computer Operator (1 Year), CCA (6 Month), Digital Marketing aur E-Commerce ki classes 15 May 2026 se shuru.", important: false },
  { id: 2, date: "2026-09-25", title: "Evening Coaching Classes", text: "LHV, CNA, CMW, Pharmacy, Lab, OTT, Dispenser, Dental aur Radiographer ki evening coaching classes shuru ho rahi hain.", important: false },
];

// Demo results — Admin page se asli results daalein aur yeh mita dein
const DEFAULT_RESULTS = [
  {
    roll: "1001", name: "Ali Ahmed", course: "DIT", semester: "1st Semester",
    subjects: [
      { name: "Computer Fundamentals", total: 100, obtained: 85 },
      { name: "MS Office", total: 100, obtained: 78 },
      { name: "English", total: 100, obtained: 72 },
      { name: "Islamiat", total: 50, obtained: 44 },
    ],
  },
  {
    roll: "1002", name: "Ayesha Khan", course: "CCA", semester: "Final",
    subjects: [
      { name: "MS Word & Excel", total: 100, obtained: 91 },
      { name: "Internet & Email", total: 100, obtained: 88 },
      { name: "InPage Urdu", total: 100, obtained: 80 },
    ],
  },
];

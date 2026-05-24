<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import MainLink from "../components/Links/MainLink.vue";
import { route } from "ziggy-js";
import { computed, ref } from "vue";
import { useToast } from "vue-toastification";

const page = usePage();
const user = computed(() => page.props.auth.user);
const openFaq = ref(null);
const toast = useToast();
const dashboardRoute = computed(() => {
    if (!user.value) return route("user.login");
    if (user.value.role === "super_admin")
        return route("super-admin.dashboard");
    if (user.value.role === "admin") return route("admin.dashboard");
    return route("employe.dashboard");
});

const employeesCount = computed(() => page.props.employees);
const adminsCount = computed(() => page.props.admins);

const faqItems = [
    {
        q: "Quel matériel faut-il ?",
        a: "Aucune pointeuse à acheter. L'administrateur affiche le QR sur un écran (borne) ou l'imprime, et chaque employé scanne avec le navigateur de son smartphone — sans application à installer.",
    },
    {
        q: "Comment limiter la fraude au pointage ?",
        a: "Chaque scan est enregistré sur le compte connecté de l'employé. Le code QR est renouvelé automatiquement toutes les ~15 secondes : un code capturé ou partagé expire vite. Chaque employé ne peut pointer qu'une fois par jour.",
    },
    {
        q: "Peut-on exporter les données ?",
        a: "Oui. L'administrateur télécharge un rapport CSV par employé (journalier, mensuel ou annuel) : date, heure, statut (à l'heure, retard, avance), écart en minutes. Vous l'importez ensuite dans votre logiciel de paie si besoin.",
    },
    {
        q: "Les horaires peuvent-ils différer par employé ?",
        a: "Oui. Pour chaque employé : heure de début et de fin, jours ouvrés (lun–dim) et tolérance de retard en minutes, configurés par l'administrateur de l'entreprise.",
    },
    {
        q: "Combien de temps pour démarrer ?",
        a: "Une fois votre espace administrateur créé, vous ajoutez les employés (avec vérification e-mail), configurez leurs horaires et affichez la borne QR. La première présence peut être enregistrée le jour même.",
    },
    {
        q: "Comment sont protégées les données ?",
        a: "Connexion HTTPS, mots de passe hashés, sessions sécurisées. Un administrateur ne voit que les employés rattachés à son entreprise. Les comptes employés n'accèdent qu'à leur propre historique.",
    },
    {
        q: "Enregistre-t-on l'arrivée et le départ ?",
        a: "Aujourd'hui, QRCoded enregistre un pointage d'arrivée par jour (avec calcul du retard ou de l'avance). Le suivi de départ en fin de journée n'est pas encore disponible.",
    },
];

const form = useForm({
    email: user.value ? user.value.email : "",
    message: "",
    nom: user.value ? user.value.nom : "",
    prenom: user.value ? user.value.prenom : "",
});

const toggleFaq = (i) => {
    openFaq.value = openFaq.value === i ? null : i;
};

function handleMessageSending() {
    form.post(route("contact.send"), {
        onSuccess: () => {
            form.reset();
            toast.success("Votre message a bien été envoyé.");
        },
        onError: () => {
            form.reset();
            toast.error(
                "Une erreur est survenue lors de l'envoi de votre message.",
            );
        },
    });
}
</script>

<template>
    <div class="homePage">
        <!-- ─── HERO ─── -->
        <section class="heroSection">
            <div class="heroBg">
                <div class="gridPattern"></div>
                <div class="glowOrb orb1"></div>
                <div class="glowOrb orb2"></div>
            </div>
            <div class="heroInner">
                <div class="heroLeft">
                    <div class="eyebrow">
                        <span class="eyebrowDot"></span>
                        Pointage par QR — sans pointeuse
                    </div>
                    <h1 class="heroTitle">
                        Le pointage<br /><em>réinventé.</em>
                    </h1>
                    <p class="heroSub">
                        Fini les pointeuses coûteuses et les tableurs manuels.
                        QRCoded permet à vos employés de pointer leur arrivée en
                        scannant un QR depuis leur smartphone, avec tableaux de
                        bord pour les managers.
                    </p>
                    <div class="heroCtas">
                        <MainLink
                            v-if="!user"
                            :link="route('user.login')"
                            text="Se connecter"
                        />
                        <MainLink
                            v-else
                            :link="dashboardRoute"
                            text="Accéder au tableau de bord"
                        />
                        <a href="#request-service" class="outlineBtn"
                            ><span>Nous contacter</span></a
                        >
                        <a href="#workflow" class="ghostBtn">
                            <span>Voir comment ça marche</span>
                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 16 16"
                                fill="none"
                            >
                                <path
                                    d="M3 8h10M9 4l4 4-4 4"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            </svg>
                        </a>
                    </div>
                </div>
                <div class="heroRight">
                    <div class="dashMockup">
                        <div class="mockHeader">
                            <div class="mockDots">
                                <span></span><span></span><span></span>
                            </div>
                            <div class="mockUrl">app.qrcoded.ma/dashboard</div>
                            <div class="mockStatus">
                                <span class="pulse"></span>Live
                            </div>
                        </div>
                        <div class="mockBody">
                            <div class="mockSidebar">
                                <div class="mockSideItem active">
                                    <span class="material-symbols-rounded"
                                        >grid_view</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >badge</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >bar_chart</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >calendar_month</span
                                    >
                                </div>
                                <div class="mockSideItem">
                                    <span class="material-symbols-rounded"
                                        >settings</span
                                    >
                                </div>
                            </div>
                            <div class="mockContent">
                                <div class="mockTopRow">
                                    <div class="mockStatCard stat1">
                                        <span class="statVal">94%</span>
                                        <span class="statLabel"
                                            >Présence aujourd'hui</span
                                        >
                                    </div>
                                    <div class="mockStatCard stat2">
                                        <span class="statVal">3</span>
                                        <span class="statLabel"
                                            >Retards signalés</span
                                        >
                                    </div>
                                    <div class="mockStatCard stat3">
                                        <span class="statVal">128</span>
                                        <span class="statLabel"
                                            >Employés actifs</span
                                        >
                                    </div>
                                </div>
                                <div class="mockChart">
                                    <div class="chartLabel">
                                        Présences — 7 derniers jours
                                    </div>
                                    <div class="chartBars">
                                        <div class="bar" style="--h: 75%">
                                            <span>L</span>
                                        </div>
                                        <div class="bar" style="--h: 90%">
                                            <span>M</span>
                                        </div>
                                        <div class="bar" style="--h: 85%">
                                            <span>M</span>
                                        </div>
                                        <div class="bar" style="--h: 95%">
                                            <span>J</span>
                                        </div>
                                        <div
                                            class="bar active"
                                            style="--h: 88%"
                                        >
                                            <span>V</span>
                                        </div>
                                        <div class="bar dim" style="--h: 30%">
                                            <span>S</span>
                                        </div>
                                        <div class="bar dim" style="--h: 10%">
                                            <span>D</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mockEmployeeList">
                                    <div class="empRow">
                                        <div class="empAvatar a1">SK</div>
                                        <div class="empInfo">
                                            <span class="empName">Salma K.</span
                                            ><span class="empTime"
                                                >Arrivée 08:47</span
                                            >
                                        </div>
                                        <div class="empBadge on-time">
                                            À l'heure
                                        </div>
                                    </div>
                                    <div class="empRow">
                                        <div class="empAvatar a2">MR</div>
                                        <div class="empInfo">
                                            <span class="empName"
                                                >Mohamed R.</span
                                            ><span class="empTime"
                                                >Arrivée 09:14</span
                                            >
                                        </div>
                                        <div class="empBadge late">+14 min</div>
                                    </div>
                                    <div class="empRow">
                                        <div class="empAvatar a3">FZ</div>
                                        <div class="empInfo">
                                            <span class="empName"
                                                >Fatima Z.</span
                                            ><span class="empTime"
                                                >Arrivée 08:59</span
                                            >
                                        </div>
                                        <div class="empBadge on-time">
                                            À l'heure
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="metricsSection">
            <div class="metricsInner">
                <div class="metricItem">
                    <div class="metricNum">Temps réel</div>
                    <div class="metricDesc">WebSocket via Laravel Reverb</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">CSV</div>
                    <div class="metricDesc">
                        rapports exportables par employé
                    </div>
                </div>
                <div class="metricDivider"></div>

                <div class="metricItem">
                    <div class="metricNum">0 DH</div>
                    <div class="metricDesc">de pointeuse à acheter</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">~15 s</div>
                    <div class="metricDesc">de validité par code QR</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">{{ adminsCount }}</div>
                    <div class="metricDesc">entreprises actives</div>
                </div>
                <div class="metricDivider"></div>
                <div class="metricItem">
                    <div class="metricNum">{{ employeesCount }}</div>
                    <div class="metricDesc">employés enregistrés</div>
                </div>
            </div>
        </section>

        <!-- ─── FEATURES ─── -->
        <section id="features" class="featuresSection">
            <div class="sectionContainer">
                <div class="sectionLabel">Fonctionnalités</div>
                <h2 class="sectionTitle">
                    Tout ce dont votre entreprise a besoin,<br /><em
                        >rien de superflu.</em
                    >
                </h2>
                <div class="featuresGrid">
                    <div class="featureCard large">
                        <div class="featureIcon">
                            <span class="material-symbols-rounded"
                                >qr_code_scanner</span
                            >
                        </div>
                        <div class="featureBody">
                            <h3>Scans QR sécurisés</h3>
                            <p>
                                Code QR dynamique, renouvelé en continu sur la
                                borne admin. Chaque employé scanne une fois par
                                jour depuis son navigateur mobile — aucune app à
                                installer.
                            </p>
                        </div>
                        <div class="featureAccent"></div>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon amber">
                            <span class="material-symbols-rounded"
                                >alarm_smart_wake</span
                            >
                        </div>
                        <h3>Calcul de Retard Intelligent</h3>
                        <p>
                            Définissez une marge de tolérance configurable. Le
                            système calcule automatiquement les minutes exactes
                            de retard par employé.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon blue">
                            <span class="material-symbols-rounded"
                                >calendar_month</span
                            >
                        </div>
                        <h3>Horaires sur mesure</h3>
                        <p>
                            Heure de début, heure de fin, jours ouvrés et
                            tolérance de retard configurables individuellement
                            pour chaque employé.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon green">
                            <span class="material-symbols-rounded"
                                >download</span
                            >
                        </div>
                        <h3>Export CSV</h3>
                        <p>
                            Rapport téléchargeable par employé (journalier,
                            mensuel ou annuel) avec résumé et détail des
                            pointages — prêt à importer dans Excel ou la paie.
                        </p>
                    </div>
                    <div class="featureCard">
                        <div class="featureIcon purple">
                            <span class="material-symbols-rounded">lock</span>
                        </div>
                        <h3>Contrôle des pointages</h3>
                        <p>
                            Scan lié au compte employé connecté, QR à courte
                            durée de vie, et blocage d'un second pointage le
                            même jour.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── WORKFLOW ─── -->
        <section id="workflow" class="workflowSection">
            <div class="sectionContainer">
                <div class="workflowGrid">
                    <div class="workflowLeft">
                        <div class="sectionLabel">
                            Organisation multi-entreprises
                        </div>
                        <h2 class="sectionTitle">
                            Trois niveaux d'<em>accès.</em>
                        </h2>
                        <p class="workflowIntro">
                            Chaque entreprise dispose de son administrateur et
                            de ses employés. Les données d'une entreprise ne
                            sont pas visibles par les autres administrateurs.
                        </p>
                        <div class="rolesStack">
                            <div class="roleItem">
                                <div class="roleIconWrap dark">
                                    <span class="material-symbols-rounded"
                                        >admin_panel_settings</span
                                    >
                                </div>
                                <div class="roleContent">
                                    <h4>Administrateur d'entreprise</h4>
                                    <p>
                                        Gère son équipe : ajout d'employés (avec
                                        code de vérification e-mail), horaires,
                                        borne QR, tableaux de bord temps réel et
                                        export CSV des présences.
                                    </p>
                                </div>
                            </div>
                            <div class="roleLine"></div>
                            <div class="roleItem">
                                <div class="roleIconWrap blue">
                                    <span class="material-symbols-rounded"
                                        >badge</span
                                    >
                                </div>
                                <div class="roleContent">
                                    <h4>Employés</h4>
                                    <p>
                                        Compte créé par leur administrateur. Ils
                                        scannent le QR pour pointer leur arrivée
                                        et consultent leur tableau de bord
                                        personnel (historique, retards, stats).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="workflowRight">
                        <div class="orgChartWrap">
                            <svg
                                class="orgChartSvg"
                                viewBox="0 0 560 400"
                                xmlns="http://www.w3.org/2000/svg"
                                aria-label="Hiérarchie QRCoded"
                            >
                                <!-- Platform node -->
                                <rect
                                    x="160"
                                    y="20"
                                    width="240"
                                    height="58"
                                    rx="10"
                                    class="node-platform"
                                />
                                <text
                                    x="280"
                                    y="44"
                                    class="node-title"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Plateforme QRCoded
                                </text>
                                <text
                                    x="280"
                                    y="62"
                                    class="node-sub"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Infrastructure partagée
                                </text>
                                <!-- Trunk -->
                                <line
                                    x1="280"
                                    y1="78"
                                    x2="280"
                                    y2="108"
                                    class="conn"
                                />
                                <line
                                    x1="120"
                                    y1="108"
                                    x2="440"
                                    y2="108"
                                    class="conn"
                                />
                                <line
                                    x1="120"
                                    y1="108"
                                    x2="120"
                                    y2="136"
                                    class="conn"
                                />
                                <line
                                    x1="440"
                                    y1="108"
                                    x2="440"
                                    y2="136"
                                    class="conn"
                                />
                                <!-- Company A -->
                                <rect
                                    x="20"
                                    y="136"
                                    width="200"
                                    height="58"
                                    rx="10"
                                    class="node-company"
                                />
                                <text
                                    x="120"
                                    y="160"
                                    class="node-title"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Entreprise A
                                </text>
                                <text
                                    x="120"
                                    y="178"
                                    class="node-sub node-sub--teal"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Données isolées
                                </text>
                                <!-- Company B -->
                                <rect
                                    x="340"
                                    y="136"
                                    width="200"
                                    height="58"
                                    rx="10"
                                    class="node-company"
                                />
                                <text
                                    x="440"
                                    y="160"
                                    class="node-title"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Entreprise B
                                </text>
                                <text
                                    x="440"
                                    y="178"
                                    class="node-sub node-sub--teal"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Données isolées
                                </text>
                                <!-- Branch A -->
                                <line
                                    x1="120"
                                    y1="194"
                                    x2="120"
                                    y2="224"
                                    class="conn"
                                />
                                <line
                                    x1="55"
                                    y1="224"
                                    x2="185"
                                    y2="224"
                                    class="conn"
                                />
                                <line
                                    x1="55"
                                    y1="224"
                                    x2="55"
                                    y2="252"
                                    class="conn"
                                />
                                <line
                                    x1="185"
                                    y1="224"
                                    x2="185"
                                    y2="252"
                                    class="conn"
                                />
                                <rect
                                    x="10"
                                    y="252"
                                    width="90"
                                    height="52"
                                    rx="8"
                                    class="node-admin"
                                />
                                <text
                                    x="55"
                                    y="272"
                                    class="node-label"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Admin
                                </text>
                                <text
                                    x="55"
                                    y="289"
                                    class="node-sublabel"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    1 par entreprise
                                </text>
                                <rect
                                    x="140"
                                    y="252"
                                    width="90"
                                    height="52"
                                    rx="8"
                                    class="node-emp"
                                />
                                <text
                                    x="185"
                                    y="272"
                                    class="node-label"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Employés
                                </text>
                                <text
                                    x="185"
                                    y="289"
                                    class="node-sublabel"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    N comptes
                                </text>
                                <!-- Branch B -->
                                <line
                                    x1="440"
                                    y1="194"
                                    x2="440"
                                    y2="224"
                                    class="conn"
                                />
                                <line
                                    x1="375"
                                    y1="224"
                                    x2="505"
                                    y2="224"
                                    class="conn"
                                />
                                <line
                                    x1="375"
                                    y1="224"
                                    x2="375"
                                    y2="252"
                                    class="conn"
                                />
                                <line
                                    x1="505"
                                    y1="224"
                                    x2="505"
                                    y2="252"
                                    class="conn"
                                />
                                <rect
                                    x="330"
                                    y="252"
                                    width="90"
                                    height="52"
                                    rx="8"
                                    class="node-admin"
                                />
                                <text
                                    x="375"
                                    y="272"
                                    class="node-label"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Admin
                                </text>
                                <text
                                    x="375"
                                    y="289"
                                    class="node-sublabel"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    1 par entreprise
                                </text>
                                <rect
                                    x="460"
                                    y="252"
                                    width="90"
                                    height="52"
                                    rx="8"
                                    class="node-emp"
                                />
                                <text
                                    x="505"
                                    y="272"
                                    class="node-label"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    Employés
                                </text>
                                <text
                                    x="505"
                                    y="289"
                                    class="node-sublabel"
                                    text-anchor="middle"
                                    dominant-baseline="central"
                                >
                                    N comptes
                                </text>
                                <!-- Legend -->
                                <rect
                                    x="100"
                                    y="336"
                                    width="360"
                                    height="50"
                                    rx="8"
                                    class="legend-box"
                                />
                                <rect
                                    x="120"
                                    y="352"
                                    width="10"
                                    height="10"
                                    rx="2"
                                    class="legend-swatch-admin"
                                />
                                <text
                                    x="136"
                                    y="357"
                                    class="legend-text"
                                    dominant-baseline="central"
                                >
                                    Gestion équipe &amp; présences
                                </text>
                                <rect
                                    x="310"
                                    y="352"
                                    width="10"
                                    height="10"
                                    rx="2"
                                    class="legend-swatch-emp"
                                />
                                <text
                                    x="326"
                                    y="357"
                                    class="legend-text"
                                    dominant-baseline="central"
                                >
                                    Pointage QR &amp; historique
                                </text>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── CONTACT ─── -->
        <section id="request-service" class="serviceRequestSection">
            <div class="sectionContainer">
                <div class="requestGrid">
                    <div class="requestInfo">
                        <div class="sectionLabel">Demande d'accès</div>
                        <h2 class="sectionTitle">
                            Prêt à moderniser votre gestion de
                            <em>présence ?</em>
                        </h2>
                        <p class="requestIntro">
                            Vous n'avez pas encore de compte administrateur ?
                            Décrivez votre besoin ci-dessous. Notre équipe lit
                            chaque message et vous répond par e-mail.
                        </p>
                        <div class="noticeBox">
                            <div class="noticeIcon">
                                <span class="material-symbols-rounded"
                                    >mail_lock</span
                                >
                            </div>
                            <div class="noticeText">
                                Ce formulaire enregistre votre demande dans
                                notre système. Si vous avez déjà un compte,
                                utilisez plutôt <strong>Se connecter</strong> en
                                haut de page.
                            </div>
                        </div>
                    </div>
                    <div class="requestFormContainer">
                        <form
                            class="interactiveForm"
                            @submit.prevent="handleMessageSending"
                        >
                            <div class="formRow">
                                <div v-if="!user" class="formGroup">
                                    <label for="nom">Nom</label>
                                    <input
                                        type="text"
                                        v-model="form.nom"
                                        id="nom"
                                        required
                                        placeholder="Ex: El Alami"
                                    />
                                </div>
                                <div v-if="!user" class="formGroup">
                                    <label for="prenom">Prénom</label>
                                    <input
                                        type="text"
                                        v-model="form.prenom"
                                        id="prenom"
                                        required
                                        placeholder="Ex: Amine"
                                    />
                                </div>
                            </div>
                            <div v-if="!user" class="formGroup">
                                <label for="email">Adresse E-mail</label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    id="email"
                                    required
                                    placeholder="Ex: contact@entreprise.ma"
                                />
                            </div>
                            <div class="formGroup">
                                <label for="message">Votre Message</label>
                                <textarea
                                    id="message"
                                    v-model="form.message"
                                    rows="6"
                                    required
                                    placeholder="Décrivez la taille de votre entreprise et vos besoins spécifiques..."
                                ></textarea>
                            </div>
                            <button type="submit" class="submitRequestBtn">
                                <span>Envoyer la demande</span>
                                <span class="material-symbols-rounded"
                                    >arrow_right_alt</span
                                >
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── FAQ ─── -->
        <section id="faq" class="faqSection">
            <div class="sectionContainer">
                <div class="sectionLabel">FAQ</div>
                <h2 class="sectionTitle">Questions fréquentes</h2>
                <div class="faqGrid">
                    <div
                        class="faqCard"
                        v-for="(item, i) in faqItems"
                        :key="i"
                        @click="toggleFaq(i)"
                        :class="{ open: openFaq === i }"
                    >
                        <div class="faqQuestion">
                            <span>{{ item.q }}</span>
                            <div class="faqToggle">
                                <svg
                                    width="14"
                                    height="14"
                                    viewBox="0 0 14 14"
                                    fill="none"
                                >
                                    <path
                                        d="M7 1v12M1 7h12"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="faqAnswer">
                            <p>{{ item.a }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── CTA ─── -->
        <section class="ctaSection">
            <div class="ctaInner">
                <div class="ctaBg"><div class="ctaGlow"></div></div>
                <div class="ctaContent">
                    <div class="ctaEyebrow">Prêt à essayer ?</div>
                    <h2>
                        Modernisez votre gestion<br /><em>dès aujourd'hui.</em>
                    </h2>
                    <p>
                        Connectez-vous si vous avez déjà un compte, ou envoyez
                        une demande d'accès via le formulaire ci-dessus.
                    </p>
                    <div class="ctaBtns">
                        <MainLink
                            v-if="!user"
                            :link="route('user.login')"
                            text="Se connecter"
                        />
                        <a v-if="!user" href="#request-service" class="ctaGhost"
                            >Demander un accès</a
                        >
                        <MainLink
                            v-else
                            :link="dashboardRoute"
                            text="Ouvrir mon application"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── FOOTER ─── -->
        <footer class="landingFooter">
            <div class="footerInner">
                <div class="footerBrand">
                    <div class="brandLogo">
                        <div class="logoMark">
                            <span class="material-symbols-rounded"
                                >qr_code_2</span
                            >
                        </div>
                        <span class="logoText"
                            >QR<span class="weight-thin">Coded</span></span
                        >
                    </div>
                    <p>
                        Infrastructure de présence pour les entreprises
                        modernes.
                    </p>
                </div>
                <div class="footerLinks">
                    <a href="#features">Fonctionnalités</a>
                    <a href="#workflow">Architecture</a>
                    <a href="#faq">FAQ</a>
                </div>
            </div>
            <div class="footerBottom">
                <p>&copy; 2026 QRcoded. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap");

.homePage {
    --bg: #0a0a0f;
    --surface: #111118;
    --surface2: #16161f;
    --border: rgba(255, 255, 255, 0.08);
    --border-strong: rgba(255, 255, 255, 0.14);
    --text-primary: #f0f0f8;
    --text-secondary: #8888aa;
    --text-muted: #55556a;
    --accent: #4f7cff;
    --accent-dim: rgba(79, 124, 255, 0.15);
    --accent-glow: rgba(79, 124, 255, 0.35);
    --green: #22c97a;
    --amber: #f5a623;
    --purple: #a78bfa;

    font-family: "Sora", sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    width: 100%;
    overflow-x: hidden;
}

* {
    box-sizing: border-box;
}

/* ── HERO ── */
.heroSection {
    position: relative;
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    overflow: hidden;
}
.heroBg {
    position: absolute;
    inset: 0;
    pointer-events: none;
}
.gridPattern {
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(
        ellipse 80% 60% at 50% 0%,
        black 0%,
        transparent 100%
    );
}
.glowOrb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    &.orb1 {
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, #4f7cff 0%, transparent 70%);
        top: -200px;
        right: -100px;
        opacity: 0.15;
    }
    &.orb2 {
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, #a78bfa 0%, transparent 70%);
        bottom: 50px;
        left: -100px;
        opacity: 0.1;
    }
}
.heroInner {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
    padding: 60px 24px 80px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
}
.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 28px;
    .eyebrowDot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--green);
        animation: blink 2s ease infinite;
    }
}
@keyframes blink {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.3;
    }
}

h1.heroTitle {
    font-size: clamp(40px, 5vw, 64px);
    font-weight: 800;
    line-height: 1.08;
    letter-spacing: -2px;
    margin: 0 0 24px;
    color: var(--text-primary);
    em {
        font-style: normal;
        color: var(--accent);
    }
}
.heroSub {
    font-size: 16px;
    color: var(--text-secondary);
    line-height: 1.7;
    margin: 0 0 40px;
    max-width: 460px;
}
.heroCtas {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.outlineBtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: 1px solid var(--border-strong);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.15s;
    &:hover {
        color: var(--text-primary);
        border-color: var(--accent);
        background: var(--accent-dim);
    }
}
.ghostBtn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.15s;
    &:hover {
        color: var(--text-primary);
    }
}

/* ── MOCKUP ── */
.dashMockup {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 14px;
    overflow: hidden;
    box-shadow:
        0 0 0 1px rgba(255, 255, 255, 0.03),
        0 40px 80px rgba(0, 0, 0, 0.6),
        0 0 80px rgba(79, 124, 255, 0.08);
}
.mockHeader {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    background: var(--surface2);
}
.mockDots {
    display: flex;
    gap: 6px;
    span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--border-strong);
    }
}
.mockUrl {
    flex: 1;
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 4px 12px;
    font-family: "DM Mono", monospace;
    font-size: 11px;
    color: var(--text-secondary);
    text-align: center;
}
.mockStatus {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: var(--green);
    .pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--green);
        box-shadow: 0 0 8px var(--green);
    }
}
.mockBody {
    display: flex;
    height: 340px;
}
.mockSidebar {
    width: 50px;
    background: var(--surface2);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px 0;
    gap: 16px;
}
.mockSideItem {
    color: var(--text-muted);
    cursor: pointer;
    span {
        font-size: 18px;
    }
    &.active {
        color: var(--accent);
    }
}
.mockContent {
    flex: 1;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    overflow: hidden;
}
.mockTopRow {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}
.mockStatCard {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    display: flex;
    flex-direction: column;
    .statVal {
        font-size: 18px;
        font-weight: 700;
    }
    .statLabel {
        font-size: 10px;
        color: var(--text-secondary);
        margin-top: 2px;
    }
    &.stat1 .statVal {
        color: var(--green);
    }
    &.stat2 .statVal {
        color: var(--amber);
    }
}
.mockChart {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.chartLabel {
    font-size: 10px;
    color: var(--text-secondary);
    font-weight: 600;
}
.chartBars {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 60px;
    padding-top: 10px;
    .bar {
        width: 24px;
        background: var(--border-strong);
        height: var(--h);
        border-radius: 3px 3px 0 0;
        position: relative;
        display: flex;
        justify-content: center;
        span {
            position: absolute;
            bottom: -16px;
            font-size: 9px;
            color: var(--text-muted);
        }
        &.active {
            background: var(--accent);
            box-shadow: 0 0 12px var(--accent-dim);
            span {
                color: var(--text-primary);
                font-weight: 600;
            }
        }
        &.dim {
            opacity: 0.4;
        }
    }
}
.mockEmployeeList {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.empRow {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 255, 255, 0.01);
    padding: 6px;
    border-radius: 6px;
}
.empAvatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    &.a1 {
        background: #4f7cff;
    }
    &.a2 {
        background: #f5a623;
    }
    &.a3 {
        background: #10b981;
    }
}
.empInfo {
    flex: 1;
    display: flex;
    flex-direction: column;
    .empName {
        font-size: 11px;
        font-weight: 600;
    }
    .empTime {
        font-size: 9px;
        color: var(--text-muted);
    }
}
.empBadge {
    font-size: 9px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 4px;
    &.on-time {
        background: rgba(34, 199, 122, 0.1);
        color: var(--green);
    }
    &.late {
        background: rgba(245, 166, 35, 0.1);
        color: var(--amber);
    }
}

/* ── METRICS ── */
.metricsSection {
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    background: #0d0d14;
}
.metricsInner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
}
.metricItem {
    flex: 1;
    min-width: 140px;
    .metricNum {
        font-size: 28px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -1px;
    }
    .metricDesc {
        font-size: 11px;
        color: var(--text-secondary);
        margin-top: 4px;
    }
}
.metricDivider {
    width: 1px;
    height: 40px;
    background: var(--border);
    flex-shrink: 0;
}

/* ── SECTION SHARED ── */
.sectionContainer {
    max-width: 1200px;
    margin: 0 auto;
    padding: 100px 24px;
}
.sectionLabel {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 12px;
}
.sectionTitle {
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -1px;
    margin: 0 0 56px;
    em {
        font-style: normal;
        color: var(--accent);
    }
}

/* ── FEATURES ── */
.featuresGrid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.featureCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 32px;
    transition:
        border-color 0.2s,
        transform 0.2s;
    &:hover {
        border-color: var(--border-strong);
        transform: translateY(-2px);
    }
    h3 {
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 12px;
    }
    p {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0;
    }
    &.large {
        grid-column: span 2;
        display: flex;
        gap: 32px;
        align-items: center;
        position: relative;
        overflow: hidden;
        .featureBody {
            flex: 1;
            position: relative;
            z-index: 2;
        }
    }
}
.featureIcon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: var(--accent-dim);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    span {
        font-size: 24px;
    }
    &.amber {
        background: rgba(245, 166, 23, 0.1);
        color: var(--amber);
    }
    &.blue {
        background: rgba(79, 124, 255, 0.1);
        color: var(--accent);
    }
    &.green {
        background: rgba(34, 199, 122, 0.1);
        color: var(--green);
    }
    &.purple {
        background: rgba(167, 139, 250, 0.1);
        color: var(--purple);
    }
}
.featureCard.large .featureIcon {
    margin-bottom: 0;
    width: 64px;
    height: 64px;
    span {
        font-size: 32px;
    }
}

/* ── WORKFLOW ── */
.workflowSection {
    background: #07070c;
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}
.workflowGrid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}
.workflowIntro {
    font-size: 15px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin: -32px 0 40px;
}
.rolesStack {
    display: flex;
    flex-direction: column;
}
.roleItem {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.roleIconWrap {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    span {
        font-size: 20px;
    }
    &.dark {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }
    &.blue {
        background: rgba(79, 124, 255, 0.1);
        color: var(--accent);
    }
}
.roleContent {
    h4 {
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 4px;
    }
    p {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
        margin: 0;
    }
}
.roleLine {
    width: 1px;
    height: 32px;
    background: var(--border);
    margin-left: 20px;
}

.orgChartWrap {
    background: var(--surface);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 32px 24px;
    box-shadow: 0 20px 48px rgba(0, 0, 0, 0.4);
}
.orgChartSvg {
    width: 100%;
    height: auto;
    display: block;
    .conn {
        stroke: rgba(255, 255, 255, 0.12);
        stroke-width: 1;
        fill: none;
    }
    .node-platform {
        fill: rgba(167, 139, 250, 0.08);
        stroke: rgba(167, 139, 250, 0.3);
        stroke-width: 1;
    }
    .node-company {
        fill: rgba(34, 199, 122, 0.06);
        stroke: rgba(34, 199, 122, 0.25);
        stroke-width: 1;
    }
    .node-admin {
        fill: rgba(79, 124, 255, 0.1);
        stroke: rgba(79, 124, 255, 0.3);
        stroke-width: 1;
    }
    .node-emp {
        fill: rgba(255, 255, 255, 0.03);
        stroke: rgba(255, 255, 255, 0.1);
        stroke-width: 1;
    }
    .node-title {
        font-family: "Sora", sans-serif;
        font-size: 13px;
        font-weight: 600;
        fill: #f0f0f8;
    }
    .node-sub {
        font-family: "Sora", sans-serif;
        font-size: 10px;
        font-weight: 400;
        fill: #8888aa;
    }
    .node-sub--teal {
        fill: rgba(34, 199, 122, 0.7);
    }
    .node-label {
        font-family: "Sora", sans-serif;
        font-size: 12px;
        font-weight: 600;
        fill: #f0f0f8;
    }
    .node-sublabel {
        font-family: "Sora", sans-serif;
        font-size: 10px;
        font-weight: 400;
        fill: #8888aa;
    }
    .legend-box {
        fill: rgba(255, 255, 255, 0.02);
        stroke: rgba(255, 255, 255, 0.07);
        stroke-width: 1;
    }
    .legend-text {
        font-family: "Sora", sans-serif;
        font-size: 10px;
        font-weight: 400;
        fill: #8888aa;
    }
    .legend-swatch-admin {
        fill: rgba(79, 124, 255, 0.6);
        stroke: rgba(79, 124, 255, 0.4);
        stroke-width: 1;
    }
    .legend-swatch-emp {
        fill: rgba(255, 255, 255, 0.2);
        stroke: rgba(255, 255, 255, 0.15);
        stroke-width: 1;
    }
}

/* ── CONTACT ── */
.serviceRequestSection {
    background: linear-gradient(
        180deg,
        var(--bg) 0%,
        var(--surface) 50%,
        var(--bg) 100%
    );
    border-bottom: 1px solid var(--border);
    position: relative;
}
.requestGrid {
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 80px;
    align-items: center;
}
.requestIntro {
    font-size: 15px;
    line-height: 1.6;
    color: var(--text-secondary);
    margin: -32px 0 32px;
}
.noticeBox {
    background: rgba(79, 124, 255, 0.05);
    border: 1px solid rgba(79, 124, 255, 0.15);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    gap: 16px;
    align-items: flex-start;
}
.noticeIcon {
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 2px;
    span {
        font-size: 22px;
    }
}
.noticeText {
    font-size: 13px;
    line-height: 1.6;
    color: #b0c2e2;
}
.requestFormContainer {
    background: var(--surface2);
    border: 1px solid var(--border-strong);
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}
.interactiveForm {
    display: flex;
    flex-direction: column;
    gap: 24px;
}
.formRow {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.formGroup {
    display: flex;
    flex-direction: column;
    gap: 8px;
    label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-primary);
        letter-spacing: 0.03em;
    }
    input,
    textarea {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 14px 16px;
        color: var(--text-primary);
        font-family: "Sora", sans-serif;
        font-size: 14px;
        transition: all 0.2s ease;
        &:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-dim);
            background: rgba(10, 10, 15, 0.5);
        }
        &::placeholder {
            color: var(--text-muted);
        }
    }
    textarea {
        resize: none;
        &::-webkit-scrollbar {
            width: 8px;
        }
        &::-webkit-scrollbar-track {
            background: transparent;
        }
        &::-webkit-scrollbar-thumb {
            background: var(--border-strong);
            border-radius: 4px;
            border: 2px solid var(--surface);
        }
        &::-webkit-scrollbar-thumb:hover {
            background: var(--accent);
        }
    }
}
.submitRequestBtn {
    background: var(--accent);
    border: none;
    border-radius: 8px;
    color: #fff;
    font-family: "Sora", sans-serif;
    font-size: 14px;
    font-weight: 600;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    margin-top: 8px;
    span {
        display: inline-flex;
        align-items: center;
    }
    &:hover {
        background: #3b66f3;
        box-shadow: 0 0 20px var(--accent-glow);
        transform: translateY(-1px);
    }
    &:active {
        transform: translateY(0);
    }
}

/* ── FAQ ── */
.faqGrid {
    max-width: 760px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.faqCard {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 24px;
    cursor: pointer;
    transition: border-color 0.15s;
    &:hover {
        border-color: var(--border-strong);
    }
    &.open {
        border-color: rgba(79, 124, 255, 0.3);
        .faqToggle {
            transform: rotate(45deg);
            color: var(--accent);
        }
        .faqAnswer {
            max-height: 200px;
            margin-top: 16px;
            opacity: 1;
        }
    }
}
.faqQuestion {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    font-size: 15px;
    font-weight: 600;
}
.faqToggle {
    color: var(--text-muted);
    transition:
        transform 0.2s,
        color 0.2s;
    display: flex;
}
.faqAnswer {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition:
        max-height 0.2s ease,
        margin 0.2s ease,
        opacity 0.2s ease;
    p {
        font-size: 13.5px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0;
    }
}

/* ── CTA ── */
.ctaSection {
    padding: 80px 24px 120px;
}
.ctaInner {
    max-width: 1200px;
    margin: 0 auto;
    background: #0d0d15;
    border: 1px solid var(--border-strong);
    border-radius: 20px;
    padding: 80px 24px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
}
.ctaBg {
    position: absolute;
    inset: 0;
    pointer-events: none;
    display: flex;
    justify-content: center;
    align-items: center;
}
.ctaGlow {
    width: 400px;
    height: 400px;
    background: radial-gradient(
        circle,
        rgba(79, 124, 255, 0.15) 0%,
        transparent 70%
    );
    filter: blur(50px);
}
.ctaContent {
    position: relative;
    z-index: 2;
    max-width: 540px;
    margin: 0 auto;
    .ctaEyebrow {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--accent);
        letter-spacing: 0.1em;
        margin-bottom: 16px;
    }
    h2 {
        font-size: clamp(28px, 4vw, 40px);
        font-weight: 800;
        letter-spacing: -1px;
        line-height: 1.15;
        margin: 0 0 16px;
        em {
            font-style: normal;
            color: var(--accent);
        }
    }
    p {
        font-size: 14px;
        color: var(--text-secondary);
        line-height: 1.6;
        margin: 0 0 32px;
    }
}
.ctaBtns {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 12px;
}
.ctaGhost {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 15px;
    border: 1px solid var(--border-strong);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all 0.15s;
    &:hover {
        color: var(--text-primary);
        border-color: var(--accent);
        background: var(--accent-dim);
    }
}

/* ── FOOTER ── */
.landingFooter {
    border-top: 1px solid var(--border);
    background: #06060a;
}
.footerInner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 64px 24px 48px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 40px;
}
.footerBrand {
    max-width: 320px;
    p {
        font-size: 12px;
        color: var(--text-muted);
        line-height: 1.5;
        margin: 12px 0 0;
    }
}
.brandLogo {
    display: flex;
    align-items: center;
    gap: 10px;
}
.logoMark {
    width: 28px;
    height: 28px;
    background: var(--accent);
    color: #fff;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    span {
        font-size: 18px;
    }
}
.logoText {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: -0.5px;
    .weight-thin {
        font-weight: 300;
        color: var(--text-secondary);
    }
}
.footerLinks {
    display: flex;
    gap: 32px;
    a {
        font-size: 13px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: color 0.15s;
        &:hover {
            color: var(--text-primary);
        }
    }
}
.footerBottom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 24px 40px;
    border-top: 1px solid rgba(255, 255, 255, 0.03);
    p {
        font-size: 11px;
        color: var(--text-muted);
        margin: 0;
    }
}

/* ── RESPONSIVE ── */
@media (max-width: 968px) {
    .heroInner,
    .featuresGrid,
    .workflowGrid,
    .requestGrid {
        grid-template-columns: 1fr;
    }
    .heroInner {
        text-align: center;
        padding-top: 40px;
    }
    .heroSub {
        margin-left: auto;
        margin-right: auto;
    }
    .heroCtas {
        justify-content: center;
    }
    .featureCard.large {
        grid-column: span 1;
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
    }
    .workflowGrid,
    .requestGrid {
        gap: 40px;
    }
    .requestFormContainer {
        padding: 30px 24px;
    }
    .footerInner {
        flex-direction: column;
        gap: 32px;
    }
    .orgChartWrap {
        padding: 20px 12px;
    }
    .metricsInner {
        gap: 16px;
    }
    .metricItem {
        min-width: 120px;
        .metricNum {
            font-size: 22px;
        }
    }
}

@media (max-width: 480px) {
    .formRow {
        grid-template-columns: 1fr;
    }
    .metricDivider {
        display: none;
    }
    .metricsInner {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px 12px;
    }
}
</style>

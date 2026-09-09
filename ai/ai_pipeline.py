import os
import joblib
import pandas as pd

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


# ============================================================
# BASE AI DIRECTORY
# ============================================================

BASE_DIR = os.path.dirname(os.path.abspath(__file__))


# ============================================================
# 1. LOAD SEVERITY MODEL
# ============================================================

severity_model = joblib.load(
    os.path.join(
        BASE_DIR,
        "severity",
        "severity_model.pkl"
    )
)

severity_vectorizer = joblib.load(
    os.path.join(
        BASE_DIR,
        "severity",
        "severity_vectorizer.pkl"
    )
)


# ============================================================
# 2. LOAD HISTORICAL COMPLAINTS
# ============================================================

historical_df = pd.read_csv(
    os.path.join(
        BASE_DIR,
        "similarity",
        "historical_complaints.csv"
    )
)

historical_complaints = (
    historical_df["complaint"]
    .fillna("")
)


# ============================================================
# 3. CREATE TF-IDF FOR HISTORICAL COMPLAINTS
# ============================================================

similarity_vectorizer = TfidfVectorizer(
    lowercase=True,
    ngram_range=(1, 2)
)

historical_tfidf = (
    similarity_vectorizer.fit_transform(
        historical_complaints
    )
)


# ============================================================
# 4. MAIN AI FUNCTION
# ============================================================

def analyze_complaint(complaint, category):

    # --------------------------------------------------------
    # VALIDATION
    # --------------------------------------------------------

    complaint = str(complaint).strip()
    category = str(category).strip()

    if not complaint:
        raise ValueError(
            "Complaint cannot be empty."
        )

    if not category:
        raise ValueError(
            "Category must be selected."
        )


    # --------------------------------------------------------
    # SEVERITY PREDICTION
    # --------------------------------------------------------

    severity_vector = (
        severity_vectorizer.transform(
            [complaint]
        )
    )

    severity = severity_model.predict(
        severity_vector
    )[0]

    severity_probabilities = (
        severity_model.predict_proba(
            severity_vector
        )[0]
    )

    severity_confidence = max(
        severity_probabilities
    )


    # --------------------------------------------------------
    # SIMILARITY SEARCH
    # --------------------------------------------------------

    complaint_vector = (
        similarity_vectorizer.transform(
            [complaint]
        )
    )

    similarity_scores = cosine_similarity(
        complaint_vector,
        historical_tfidf
    )[0]


    # --------------------------------------------------------
    # FIND SIMILAR COMPLAINTS
    # --------------------------------------------------------

    threshold = 0.45

    matching_indices = [
        i
        for i, score in enumerate(
            similarity_scores
        )
        if score >= threshold
    ]

    matching_indices.sort(
        key=lambda i: similarity_scores[i],
        reverse=True
    )


    # --------------------------------------------------------
    # FILTER SIMILAR COMPLAINTS
    # BY MANUALLY SELECTED CATEGORY
    # --------------------------------------------------------

    category_matches = []

    for i in matching_indices:

        historical_category = (
            str(
                historical_df.iloc[i]["category"]
            ).strip()
        )

        if historical_category == category:

            category_matches.append({

                "complaint":
                    historical_df.iloc[i]["complaint"],

                "category":
                    historical_category,

                "room":
                    historical_df.iloc[i]["room"],

                "similarity":
                    round(
                        float(
                            similarity_scores[i]
                        ),
                        3
                    )
            })


    # --------------------------------------------------------
    # RECURRING COMPLAINT DETECTION
    # --------------------------------------------------------

    recurring = (
        len(category_matches) >= 2
    )


    # --------------------------------------------------------
    # RETURN COMPLETE AI RESULT
    # --------------------------------------------------------

    return {

        "complaint":
            complaint,

        # Category is NOT predicted by AI.
        # It comes from the user's manual selection.
        "category":
            category,

        "severity":
            severity,

        "severity_confidence":
            round(
                float(
                    severity_confidence
                ),
                3
            ),

        "recurring":
            recurring,

        "similar_count":
            len(category_matches),

        "similar_complaints":
            category_matches[:5]
    }


# ============================================================
# 5. LOCAL TEST
# ============================================================

if __name__ == "__main__":

    test_complaints = [

        {
            "complaint":
                "My fan is running very slowly",

            "category":
                "Electrical"
        },

        {
            "complaint":
                "The bathroom tap has very low water flow",

            "category":
                "Plumbing & Drainage"
        },

        {
            "complaint":
                "My room light keeps flickering",

            "category":
                "Electrical"
        },

        {
            "complaint":
                "There is a problem with my chair",

            "category":
                "Furniture & Room Fixtures"
        }
    ]


    for item in test_complaints:

        result = analyze_complaint(
            item["complaint"],
            item["category"]
        )

        print("\n================================")
        print("AI ANALYSIS")
        print("================================")

        print(
            "Complaint:",
            result["complaint"]
        )

        print(
            "Category:",
            result["category"]
        )

        print(
            "Severity:",
            result["severity"]
        )

        print(
            "Severity confidence:",
            result["severity_confidence"]
        )

        print(
            "Recurring:",
            result["recurring"]
        )

        print(
            "Similar complaints:",
            result["similar_count"]
        )

        print(
            "\nSimilar historical complaints:"
        )

        for match in result[
            "similar_complaints"
        ]:

            print(
                f"{match['similarity']} | "
                f"{match['category']} | "
                f"{match['room']} | "
                f"{match['complaint']}"
            )
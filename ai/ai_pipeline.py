import os
import joblib
import pandas as pd

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


# ============================================================
# BASE AI DIRECTORY
# ============================================================

BASE_DIR = os.path.dirname(
    os.path.abspath(__file__)
)


# ============================================================
# 1. LOAD CATEGORY MODEL
# ============================================================

category_model = joblib.load(
    os.path.join(
        BASE_DIR,
        "category",
        "category_model.pkl"
    )
)

category_vectorizer = joblib.load(
    os.path.join(
        BASE_DIR,
        "category",
        "category_vectorizer.pkl"
    )
)


# ============================================================
# 2. LOAD SEVERITY MODEL
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
# 3. LOAD HISTORICAL COMPLAINTS
# ============================================================

historical_df = pd.read_csv(
    os.path.join(
        BASE_DIR,
        "similarity",
        "historical_complaints.csv"
    )
)

historical_complaints = (
    historical_df["complaint"].fillna("")
)


# ============================================================
# 4. CREATE TF-IDF FOR HISTORICAL COMPLAINTS
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
# 5. MAIN AI FUNCTION
# ============================================================

def analyze_complaint(complaint):

    # --------------------------------------------------------
    # CATEGORY MODEL PREDICTION
    # --------------------------------------------------------

    category_vector = (
        category_vectorizer.transform(
            [complaint]
        )
    )

    model_category = category_model.predict(
        category_vector
    )[0]

    category_probabilities = (
        category_model.predict_proba(
            category_vector
        )[0]
    )

    model_category_confidence = max(
        category_probabilities
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
    # FIND STRONG SIMILAR COMPLAINTS
    # --------------------------------------------------------

    threshold = 0.45

    matching_indices = [
        i
        for i, score in enumerate(similarity_scores)
        if score >= threshold
    ]

    matching_indices.sort(
        key=lambda i: similarity_scores[i],
        reverse=True
    )


    # --------------------------------------------------------
    # FINAL CATEGORY DECISION
    #
    # Strong historical similarity is trusted over a
    # low-confidence category model prediction.
    # --------------------------------------------------------

    category = model_category
    category_confidence = model_category_confidence

    if matching_indices:

        strongest_index = matching_indices[0]

        strongest_similarity = similarity_scores[
            strongest_index
        ]

        historical_category = (
            historical_df.iloc[
                strongest_index
            ]["category"]
        )

        # Strong historical match
        if strongest_similarity >= 0.45:

            category = historical_category

            category_confidence = (
                strongest_similarity
            )

    else:

        # If the classifier itself is not confident,
        # don't present a random category as certain.
        if model_category_confidence < 0.30:

            category = "Unknown"


    # --------------------------------------------------------
    # GET SIMILAR COMPLAINTS FROM FINAL CATEGORY
    # --------------------------------------------------------

    category_matches = []

    for i in matching_indices:

        historical_category = (
            historical_df.iloc[i]["category"]
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

        "category":
            category,

        "category_confidence":
            round(
                float(category_confidence),
                3
            ),

        "severity":
            severity,

        "severity_confidence":
            round(
                float(severity_confidence),
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
# 6. TEST
# ============================================================

if __name__ == "__main__":

    test_complaints = [

        "My fan is running very slowly",

        "The bathroom tap has very low water flow",

        "My room light keeps flickering",

        "There is a problem with my chair"
    ]


    for complaint in test_complaints:

        result = analyze_complaint(
            complaint
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
            "Category confidence:",
            result["category_confidence"]
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
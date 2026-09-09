import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


# Load historical complaints
df = pd.read_csv("historical_complaints.csv")

complaints = df["complaint"].fillna("")


# Convert historical complaints to TF-IDF
vectorizer = TfidfVectorizer(
    lowercase=True,
    ngram_range=(1, 2)
)

tfidf_matrix = vectorizer.fit_transform(complaints)


def detect_recurring_issue(new_complaint, threshold=0.45):

    new_vector = vectorizer.transform([new_complaint])

    similarities = cosine_similarity(
        new_vector,
        tfidf_matrix
    )[0]

    matching_indices = [
        i for i, score in enumerate(similarities)
        if score >= threshold
    ]

    matching_indices.sort(
        key=lambda i: similarities[i],
        reverse=True
    )

    if not matching_indices:
        return {
            "recurring": False,
            "count": 0,
            "category": None,
            "matches": []
        }

    categories = [
        df.iloc[i]["category"]
        for i in matching_indices
    ]

    main_category = pd.Series(categories).value_counts().index[0]

    matches = []

    for i in matching_indices:

        if df.iloc[i]["category"] == main_category:

            matches.append({
                "complaint": df.iloc[i]["complaint"],
                "category": df.iloc[i]["category"],
                "room": df.iloc[i]["room"],
                "similarity": round(
                    float(similarities[i]), 3
                )
            })

    # At least 3 historical similar complaints = recurring
    recurring = len(matches) >= 2

    return {
        "recurring": recurring,
        "count": len(matches),
        "category": main_category,
        "matches": matches
    }


# -------------------------------
# TEST
# -------------------------------

test_complaints = [
    "My fan is running very slowly",
    "The bathroom tap has very low water flow",
    "My room light keeps flickering",
    "There is a problem with my chair"
]


for complaint in test_complaints:

    result = detect_recurring_issue(complaint)

    print("\n================================")
    print("NEW COMPLAINT:")
    print(complaint)
    print("================================")

    print("Recurring:", result["recurring"])
    print("Similar complaints:", result["count"])
    print("Category:", result["category"])

    print("\nMatching historical complaints:")

    for match in result["matches"]:

        print(
            f"{match['similarity']} | "
            f"{match['category']} | "
            f"{match['room']} | "
            f"{match['complaint']}"
        )
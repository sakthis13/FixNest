import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


# Load your complaint dataset
df = pd.read_csv(r"..\data\seed_dataset.csv")

complaints = df["complaint"].fillna("")


# Convert all complaints into TF-IDF vectors
vectorizer = TfidfVectorizer(
    lowercase=True,
    ngram_range=(1, 2)
)

tfidf_matrix = vectorizer.fit_transform(complaints)


def find_similar_complaints(new_complaint, top_n=5):

    # Convert new complaint into the same TF-IDF space
    new_vector = vectorizer.transform([new_complaint])

    # Calculate similarity with every existing complaint
    similarity_scores = cosine_similarity(
        new_vector,
        tfidf_matrix
    )[0]

    # Get highest similarity scores
    top_indices = similarity_scores.argsort()[::-1][:top_n]

    results = []

    for index in top_indices:
        results.append({
            "complaint": df.iloc[index]["complaint"],
            "category": df.iloc[index]["category"],
            "similarity": round(float(similarity_scores[index]), 3)
        })

    return results


# -------------------------------
# TEST
# -------------------------------

test_complaints = [
    "The fan in my room is running very slowly",
    "There is a problem with the bathroom water flow",
    "My room light keeps flickering"
]

for complaint in test_complaints:

    print("\n================================")
    print("NEW COMPLAINT:")
    print(complaint)
    print("================================")

    results = find_similar_complaints(complaint)

    for result in results:
        print(
            f"{result['similarity']} | "
            f"{result['category']} | "
            f"{result['complaint']}"
        )